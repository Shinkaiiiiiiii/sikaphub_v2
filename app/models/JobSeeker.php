<?php

class JobSeeker
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // =========================================================================
    // V2 RELATIONAL BRIDGE
    // =========================================================================

    public function getJobseekerIdByUserId($userId)
    {
        $sql = "SELECT jobseeker_id FROM job_seekers WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $result = $stmt->fetch();
        return $result ? $result['jobseeker_id'] : false;
    }

    // Retrieves the user's home municipality for feed filtering
    public function getHomeMunicipalityId($jobseekerId)
    {
        $sql = "SELECT home_municipality_id FROM job_seekers WHERE jobseeker_id = :jobseeker_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':jobseeker_id' => $jobseekerId]);
        $result = $stmt->fetch();
        return $result ? $result['home_municipality_id'] : null;
    }

    // =========================================================================
    // CORE DASHBOARD QUERIES (3NF UPGRADED)
    // =========================================================================

    public function getAllOpenJobs()
    {
        $sql = "SELECT jp.job_id, jp.job_title, jp.salary_range, jp.employment_type, jp.date_posted, 
                       e.company_name, m.municipality_name
                FROM job_postings jp
                JOIN employers e ON jp.employer_id = e.employer_id
                JOIN lib_municipalities m ON jp.municipality_id = m.municipality_id
                WHERE jp.job_status = 'Open'
                ORDER BY jp.date_posted DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================================================================
    // APPLICATION TRANSACTIONS
    // =========================================================================

    public function hasAlreadyApplied($jobseekerId, $jobId)
    {
        $sql = "SELECT application_id FROM applications WHERE jobseeker_id = :jobseeker_id AND job_id = :job_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':jobseeker_id' => $jobseekerId,
            ':job_id' => $jobId
        ]);
        return $stmt->fetch() ? true : false;
    }

    public function applyForJob($jobseekerId, $jobId, $matchScore)
    {
        try {
            $this->db->beginTransaction();

            $sql = "INSERT INTO applications (jobseeker_id, job_id, ai_match_score, application_status) 
                    VALUES (:jobseeker_id, :job_id, :ai_match_score, 'Pending')";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':jobseeker_id' => $jobseekerId,
                ':job_id' => $jobId,
                ':ai_match_score' => $matchScore
            ]);

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Application Transaction Failed: " . $e->getMessage());
            return false;
        }
    }

    public function getMyApplications($jobseekerId)
    {
        $sql = "SELECT 
                    a.application_id, 
                    a.application_status, 
                    a.application_date, 
                    a.ai_match_score,
                    jp.job_title, 
                    e.company_name
                FROM applications a
                JOIN job_postings jp ON a.job_id = jp.job_id
                JOIN employers e ON jp.employer_id = e.employer_id
                WHERE a.jobseeker_id = :jobseeker_id
                ORDER BY a.application_date DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':jobseeker_id' => $jobseekerId]);
        return $stmt->fetchAll();
    }

    // =========================================================================
    // PROFILE UPSERT TRANSACTIONS
    // =========================================================================

    public function saveCompleteProfile($jobseekerId, $data)
    {
        try {
            $this->db->beginTransaction();

            // 1. Update Core job_seekers Table
            $sqlCore = "UPDATE job_seekers 
                        SET first_name = :first_name, 
                            last_name = :last_name, 
                            home_municipality_id = :home_municipality_id, 
                            profile_visibility = :visibility";
            
            // Only update file paths if new files were uploaded and verified
            if (!empty($data['profile_photo'])) $sqlCore .= ", profile_photo = :profile_photo";
            if (!empty($data['resume_file'])) $sqlCore .= ", resume_file = :resume_file";
            
            $sqlCore .= " WHERE jobseeker_id = :jobseeker_id";

            $stmtCore = $this->db->prepare($sqlCore);
            $coreParams = [
                ':first_name' => $data['first_name'],
                ':last_name' => $data['last_name'],
                ':home_municipality_id' => $data['home_municipality_id'],
                ':visibility' => $data['visibility'],
                ':jobseeker_id' => $jobseekerId
            ];
            if (!empty($data['profile_photo'])) $coreParams[':profile_photo'] = $data['profile_photo'];
            if (!empty($data['resume_file'])) $coreParams[':resume_file'] = $data['resume_file'];
            $stmtCore->execute($coreParams);

            // 2. UPSERT Job Preferences (1-to-1: Delete then Insert)
            $this->db->prepare("DELETE FROM job_preferences WHERE jobseeker_id = ?")->execute([$jobseekerId]);
            $prefs = $data['preferences'];
            $sqlPrefs = "INSERT INTO job_preferences (jobseeker_id, desired_job_type, expected_salary, preferred_work_setup) VALUES (?, ?, ?, ?)";
            $this->db->prepare($sqlPrefs)->execute([
                $jobseekerId, 
                $prefs['desired_job_type'], 
                $prefs['expected_salary'], 
                $prefs['preferred_work_setup']
            ]);

            // 3. Sync Work Experience (Wipe and Replace)
            $this->db->prepare("DELETE FROM work_experience WHERE jobseeker_id = ?")->execute([$jobseekerId]);
            if (!empty($data['experience'])) {
                $stmtExp = $this->db->prepare("INSERT INTO work_experience (jobseeker_id, job_title, company_name, start_date, end_date) VALUES (?, ?, ?, ?, ?)");
                foreach ($data['experience'] as $exp) {
                    $stmtExp->execute([
                        $jobseekerId,
                        $exp['job_title'],
                        $exp['company_name'],
                        $exp['start_date'],
                        empty($exp['end_date']) ? null : $exp['end_date']
                    ]);
                }
            }

            // 4. Sync Education (Wipe and Replace)
            $this->db->prepare("DELETE FROM education WHERE jobseeker_id = ?")->execute([$jobseekerId]);
            if (!empty($data['education'])) {
                $stmtEdu = $this->db->prepare("INSERT INTO education (jobseeker_id, degree_level, school_name, year_graduated) VALUES (?, ?, ?, ?)");
                foreach ($data['education'] as $edu) {
                    $stmtEdu->execute([
                        $jobseekerId,
                        $edu['degree_level'],
                        $edu['school_name'],
                        $edu['year_graduated']
                    ]);
                }
            }

            // 5. Sync Preferred Locations (Wipe and Replace)
            $this->db->prepare("DELETE FROM preferred_work_locations WHERE jobseeker_id = ?")->execute([$jobseekerId]);
            if (!empty($prefs['preferred_municipality_ids'])) {
                $stmtLoc = $this->db->prepare("INSERT INTO preferred_work_locations (jobseeker_id, municipality_id) VALUES (?, ?)");
                foreach ($prefs['preferred_municipality_ids'] as $munId) {
                    $stmtLoc->execute([$jobseekerId, $munId]);
                }
            }

            // 6. Sync Core Skills (Dynamic Resolution)
            $this->db->prepare("DELETE FROM jobseeker_skills WHERE jobseeker_id = ?")->execute([$jobseekerId]);
            if (!empty($data['custom_skills'])) {
                $stmtCheck = $this->db->prepare("SELECT skill_id FROM master_skills WHERE skill_name = ? LIMIT 1");
                $stmtInsert = $this->db->prepare("INSERT INTO master_skills (category_id, skill_name, status) VALUES (7, ?, 'pending')");
                $stmtLink = $this->db->prepare("INSERT INTO jobseeker_skills (jobseeker_id, skill_id, proficiency_level) VALUES (?, ?, 'Intermediate')");

                foreach ($data['custom_skills'] as $skillName) {
                    $stmtCheck->execute([$skillName]);
                    $skillRow = $stmtCheck->fetch();
                    
                    if ($skillRow) {
                        $skillId = $skillRow['skill_id'];
                    } else {
                        $stmtInsert->execute([$skillName]);
                        $skillId = $this->db->lastInsertId();
                    }
                    $stmtLink->execute([$jobseekerId, $skillId]);
                }
            }

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Profile UPSERT Failed: " . $e->getMessage());
            return false;
        }
    }
}