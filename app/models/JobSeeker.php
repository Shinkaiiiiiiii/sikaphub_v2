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

    // Translates the authenticated User ID into the JobSeeker Primary Key
    public function getJobseekerIdByUserId($userId)
    {
        $sql = "SELECT jobseeker_id FROM job_seekers WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $result = $stmt->fetch();
        return $result ? $result['jobseeker_id'] : false;
    }

    // =========================================================================
    // CORE DASHBOARD QUERIES
    // =========================================================================

    public function getAllOpenJobs()
    {
        $sql = "SELECT jp.job_id, jp.job_title, jp.salary_range, jp.employment_type, jp.date_posted, 
                       e.company_name, b.barangay_name
                FROM job_postings jp
                JOIN employers e ON jp.employer_id = e.employer_id
                JOIN barangays b ON jp.barangay_id = b.barangay_id
                WHERE jp.job_status = 'Open'
                ORDER BY jp.date_posted DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // =========================================================================
    // APPLICATION TRANSACTIONS
    // =========================================================================

    // Check if the user has already applied to this specific job
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

    // Execute the Point-in-Time Application Transaction
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

    // Fetch all applications submitted by the active user (Updated for V2 Relational ID)
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