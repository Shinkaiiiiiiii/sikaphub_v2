<?php

class Employer
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Fetch the Employer ID linked to the current User Session
    public function getEmployerId($userId)
    {
        $sql = "SELECT employer_id FROM Employers WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $result = $stmt->fetch();
        return $result ? $result['employer_id'] : false;
    }

    // Fetch all jobs posted by this employer
    public function getEmployerJobs($employerId)
    {
        $sql = "SELECT job_id, job_title, job_status, date_posted 
                FROM Job_Postings 
                WHERE employer_id = :employer_id 
                ORDER BY date_posted DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':employer_id' => $employerId]);
        return $stmt->fetchAll();
    }

    // Fetch applicants for a specific job, STRICTLY ordered by AI Match Score
    public function getRankedApplicantsForJob($jobId)
    {
        $sql = "SELECT 
                    a.application_id, 
                    a.ai_match_score, 
                    a.application_status, 
                    a.application_date,
                    js.first_name, 
                    js.last_name, 
                    js.contact_number
                FROM Applications a
                JOIN Job_Seekers js ON a.jobseeker_id = js.jobseeker_id
                WHERE a.job_id = :job_id
                ORDER BY a.ai_match_score DESC, a.application_date ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':job_id' => $jobId]);
        return $stmt->fetchAll();
    }
}