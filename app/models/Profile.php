<?php

class Profile
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // =========================================================================
    // BASELINE METHODS (RETAINED FOR BACKWARD COMPATIBILITY)
    // =========================================================================

    public function fetchAllMasterSkills()
    {
        // Enforced lowercase for Linux safety
        $sql = "SELECT * FROM master_skills ORDER BY skill_name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateJobSeekerSkills($jobseekerId, $skillIds, $proficiencyLevel = 'Intermediate')
    {
        try {
            $this->db->beginTransaction();

            $deleteSql = "DELETE FROM jobseeker_skills WHERE jobseeker_id = :jobseeker_id";
            $deleteStmt = $this->db->prepare($deleteSql);
            $deleteStmt->bindParam(':jobseeker_id', $jobseekerId);
            $deleteStmt->execute();

            $insertSql = "INSERT INTO jobseeker_skills (jobseeker_id, skill_id, proficiency_level) 
                          VALUES (:jobseeker_id, :skill_id, :proficiency_level)";
            $insertStmt = $this->db->prepare($insertSql);

            foreach ($skillIds as $skillId) {
                $insertStmt->bindValue(':jobseeker_id', $jobseekerId);
                $insertStmt->bindValue(':skill_id', $skillId);
                $insertStmt->bindValue(':proficiency_level', $proficiencyLevel);
                $insertStmt->execute();
            }

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Transaction Failed: " . $e->getMessage());
            return false;
        }
    }

    // =========================================================================
    // V2 DYNAMIC PROFILE ENGINE: HELPER / LOOKUP METHODS
    // =========================================================================

    public function getAllApprovedSkills()
    {
        $sql = "SELECT skill_id, skill_name FROM master_skills WHERE status = 'approved' ORDER BY skill_name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getBarangays()
    {
        $sql = "SELECT barangay_id, barangay_name FROM barangays ORDER BY barangay_name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getMunicipalities()
    {
        $sql = "SELECT municipality_id, municipality_name, province_name FROM lib_municipalities ORDER BY province_name ASC, municipality_name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getSeekerProfile($userId)
    {
        $sql = "SELECT * FROM job_seekers WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getEmployerProfile($userId)
    {
        $sql = "SELECT * FROM employers WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function getJobseekerId($userId)
    {
        $sql = "SELECT jobseeker_id FROM job_seekers WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $result = $stmt->fetch();
        return $result ? $result['jobseeker_id'] : false;
    }

    // =========================================================================
    // V2 DYNAMIC PROFILE ENGINE: UNIFIED TRANSACTIONS
    // =========================================================================

    public function saveCompleteSeekerProfile($payload)
    {
        try {
            $this->db->beginTransaction();

            $jobseekerId = $this->getJobseekerId($payload['user_id']);

            // 0. UPSERT Job Seeker Identity & Home Location
            if (!$jobseekerId) {
                // User doesn't exist in job_seekers yet -> INSERT
                $sqlAssets = "INSERT INTO job_seekers (user_id, first_name, last_name, home_municipality_id, profile_visibility, profile_photo, resume_file) 
                              VALUES (:user_id, :first_name, :last_name, :home_mun_id, :visibility, :photo, :resume)";
                $stmtAssets = $this->db->prepare($sqlAssets);
                $stmtAssets->execute([
                    ':user_id' => $payload['user_id'],
                    ':first_name' => $payload['first_name'],
                    ':last_name' => $payload['last_name'],
                    ':home_mun_id' => $payload['home_municipality_id'],
                    ':visibility' => $payload['visibility'],
                    ':photo' => $payload['profile_photo'],
                    ':resume' => $payload['resume_file']
                ]);
                $jobseekerId = $this->db->lastInsertId(); // Capture the new ID!
            } else {
                // User exists -> UPDATE
                $sqlAssets = "UPDATE job_seekers SET first_name = :first_name, last_name = :last_name, home_municipality_id = :home_mun_id, profile_visibility = :visibility, profile_photo = :photo, resume_file = :resume WHERE jobseeker_id = :jobseeker_id";
                $stmtAssets = $this->db->prepare($sqlAssets);
                $stmtAssets->execute([
                    ':first_name' => $payload['first_name'],
                    ':last_name' => $payload['last_name'],
                    ':home_mun_id' => $payload['home_municipality_id'],
                    ':visibility' => $payload['visibility'],
                    ':photo' => $payload['profile_photo'],
                    ':resume' => $payload['resume_file'],
                    ':jobseeker_id' => $jobseekerId
                ]);
            }

            // 1. Insert or Update Job Preferences
            $pref = $payload['preferences'];
            $sqlPrefCheck = "SELECT preference_id FROM job_preferences WHERE jobseeker_id = :jobseeker_id LIMIT 1";
            $stmtPrefCheck = $this->db->prepare($sqlPrefCheck);
            $stmtPrefCheck->execute([':jobseeker_id' => $jobseekerId]);

            if ($stmtPrefCheck->fetch()) {
                $sqlPref = "UPDATE job_preferences SET desired_job_type = :desired, expected_salary = :salary, preferred_work_setup = :setup WHERE jobseeker_id = :jobseeker_id";
            } else {
                $sqlPref = "INSERT INTO job_preferences (jobseeker_id, desired_job_type, expected_salary, preferred_work_setup) VALUES (:jobseeker_id, :desired, :salary, :setup)";
            }
            $stmtPref = $this->db->prepare($sqlPref);
            $stmtPref->execute([
                ':jobseeker_id' => $jobseekerId,
                ':desired' => $pref['desired_job_type'],
                ':salary' => $pref['expected_salary'],
                ':setup' => $pref['preferred_work_setup']
            ]);

            // 1.5 Wipe and Replace Preferred Work Locations
            $this->db->prepare("DELETE FROM preferred_work_locations WHERE jobseeker_id = ?")->execute([$jobseekerId]);
            if (!empty($payload['preferences']['preferred_municipality_ids']) && is_array($payload['preferences']['preferred_municipality_ids'])) {
                $sqlWorkLocs = "INSERT INTO preferred_work_locations (jobseeker_id, municipality_id) VALUES (:js_id, :mun_id)";
                $stmtWorkLocs = $this->db->prepare($sqlWorkLocs);
                foreach ($payload['preferences']['preferred_municipality_ids'] as $munId) {
                    $stmtWorkLocs->execute([
                        ':js_id' => $jobseekerId,
                        ':mun_id' => $munId
                    ]);
                }
            }

            // 2. Wipe and Replace Education Array
            $this->db->prepare("DELETE FROM education WHERE jobseeker_id = ?")->execute([$jobseekerId]);
            if (!empty($payload['education'])) {
                $sqlEdu = "INSERT INTO education (jobseeker_id, degree_level, school_name, year_graduated) VALUES (:js_id, :degree, :school, :year)";
                $stmtEdu = $this->db->prepare($sqlEdu);
                foreach ($payload['education'] as $edu) {
                    $stmtEdu->execute([
                        ':js_id' => $jobseekerId,
                        ':degree' => $edu['degree_level'],
                        ':school' => $edu['school_name'],
                        ':year' => $edu['year_graduated']
                    ]);
                }
            }

            // 3. Wipe and Replace Work Experience Array
            $this->db->prepare("DELETE FROM work_experience WHERE jobseeker_id = ?")->execute([$jobseekerId]);
            if (!empty($payload['experience'])) {
                $sqlExp = "INSERT INTO work_experience (jobseeker_id, job_title, company_name, start_date, end_date, job_description) VALUES (:js_id, :title, :company, :start, :end, :desc)";
                $stmtExp = $this->db->prepare($sqlExp);
                foreach ($payload['experience'] as $exp) {
                    $stmtExp->execute([
                        ':js_id' => $jobseekerId,
                        ':title' => $exp['job_title'],
                        ':company' => $exp['company_name'],
                        ':start' => $exp['start_date'],
                        ':end' => $exp['end_date'],
                        ':desc' => $exp['job_description']
                    ]);
                }
            }

            // 4. Process Skills Engine
            $finalSkillIds = $payload['standard_skills'];

            if (!empty($payload['custom_skills'])) {
                $sqlInsertSkill = "INSERT IGNORE INTO master_skills (category_id, skill_name, status) VALUES (1, :skill_name, 'pending')";
                $stmtInsertSkill = $this->db->prepare($sqlInsertSkill);
                $sqlFetchSkill = "SELECT skill_id FROM master_skills WHERE skill_name = :skill_name LIMIT 1";
                $stmtFetchSkill = $this->db->prepare($sqlFetchSkill);

                foreach ($payload['custom_skills'] as $customSkill) {
                    $stmtInsertSkill->execute([':skill_name' => $customSkill]);
                    $stmtFetchSkill->execute([':skill_name' => $customSkill]);
                    $skillRow = $stmtFetchSkill->fetch();
                    if ($skillRow) {
                        $finalSkillIds[] = $skillRow['skill_id'];
                    }
                }
            }

            $finalSkillIds = array_unique($finalSkillIds);

            $this->db->prepare("DELETE FROM jobseeker_skills WHERE jobseeker_id = ?")->execute([$jobseekerId]);
            if (!empty($finalSkillIds)) {
                $sqlSkillLink = "INSERT INTO jobseeker_skills (jobseeker_id, skill_id, proficiency_level) VALUES (:js_id, :skill_id, 'Intermediate')";
                $stmtSkillLink = $this->db->prepare($sqlSkillLink);
                foreach ($finalSkillIds as $sId) {
                    $stmtSkillLink->execute([
                        ':js_id' => $jobseekerId,
                        ':skill_id' => $sId
                    ]);
                }
            }

            // 5. Activate Account Status
            $sqlActivate = "UPDATE users SET account_status = 'Active', updated_at = CURRENT_TIMESTAMP WHERE user_id = :user_id";
            $stmtActivate = $this->db->prepare($sqlActivate);
            $stmtActivate->execute([':user_id' => $payload['user_id']]);

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            die("<div style='background:#111;color:#0f0;padding:20px;font-family:monospace;font-size:16px;'><b>SQL FATAL ERROR:</b><br><br>" . $e->getMessage() . "</div>");
        }
    }

    // =========================================================================
    // EMPLOYER TRANSACTION (V2 EXPANDED)
    // =========================================================================

    public function saveEmployerProfile($payload)
    {
        try {
            $sql = "UPDATE employers SET 
                        company_phone = :company_phone,
                        industry = :industry,
                        company_size = :company_size,
                        company_logo = :company_logo,
                        company_description = :company_description,
                        website_url = :website_url,
                        facebook_url = :facebook_url,
                        linkedin_url = :linkedin_url,
                        twitter_url = :twitter_url
                    WHERE user_id = :user_id";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':company_phone' => $payload['company_phone'],
                ':industry' => $payload['industry'],
                ':company_size' => $payload['company_size'],
                ':company_logo' => $payload['company_logo'],
                ':company_description' => $payload['company_description'],
                ':website_url' => $payload['website_url'],
                ':facebook_url' => $payload['facebook_url'],
                ':linkedin_url' => $payload['linkedin_url'],
                ':twitter_url' => $payload['twitter_url'],
                ':user_id' => $payload['user_id']
            ]);
        } catch (PDOException $e) {
            error_log("Employer Profile Update Failed: " . $e->getMessage());
            return false;
        }
    }
}