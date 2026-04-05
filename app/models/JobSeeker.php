<?php

class JobSeeker
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllOpenJobs()
    {
        $sql = "SELECT jp.job_id, jp.job_title, jp.salary_range, jp.employment_type, jp.date_posted, 
                       e.company_name, b.barangay_name
                FROM Job_Postings jp
                JOIN Employers e ON jp.employer_id = e.employer_id
                JOIN Barangays b ON jp.barangay_id = b.barangay_id
                WHERE jp.job_status = 'Open'
                ORDER BY jp.date_posted DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Check if the user has already applied to this specific job
    public function hasAlreadyApplied($jobseekerId, $jobId)
    {
        $sql = "SELECT application_id FROM Applications WHERE jobseeker_id = :jobseeker_id AND job_id = :job_id LIMIT 1";
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

            $sql = "INSERT INTO Applications (jobseeker_id, job_id, ai_match_score, application_status) 
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
}