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

    public function getAllOpenJobs($jobseekerId, $homeMunicipalityId)
    {
        // 3NF Upgrade: Join against lib_municipalities and preferred_work_locations
        $sql = "SELECT jp.job_id, jp.job_title, jp.salary_range, jp.employment_type, jp.date_posted, 
                       e.company_name, m.municipality_name
                FROM job_postings jp
                JOIN employers e ON jp.employer_id = e.employer_id
                JOIN lib_municipalities m ON jp.municipality_id = m.municipality_id
                LEFT JOIN preferred_work_locations pwl ON jp.municipality_id = pwl.municipality_id AND pwl.jobseeker_id = :jobseeker_id
                WHERE jp.job_status = 'Open'
                AND (jp.municipality_id = :home_municipality_id OR pwl.municipality_id IS NOT NULL)
                ORDER BY jp.date_posted DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':jobseeker_id' => $jobseekerId,
            ':home_municipality_id' => $homeMunicipalityId
        ]);
        return $stmt->fetchAll();
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
}