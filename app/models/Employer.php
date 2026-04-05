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

    // Fetch core application and seeker profile, strictly gated by Employer ID
    public function getApplicationDetails($applicationId, $employerId)
    {
        $sql = "SELECT 
                    a.application_id, a.jobseeker_id, a.ai_match_score, a.application_status, a.application_date,
                    js.first_name, js.last_name, js.gender, js.contact_number, js.street_address,
                    b.barangay_name,
                    jp.job_title
                FROM Applications a
                JOIN Job_Postings jp ON a.job_id = jp.job_id
                JOIN Job_Seekers js ON a.jobseeker_id = js.jobseeker_id
                JOIN Barangays b ON js.barangay_id = b.barangay_id
                WHERE a.application_id = :app_id AND jp.employer_id = :employer_id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':app_id' => $applicationId,
            ':employer_id' => $employerId
        ]);
        return $stmt->fetch();
    }

    // Fetch 1:N Education History
    public function getSeekerEducation($jobseekerId)
    {
        $sql = "SELECT degree_level, school_name, year_graduated 
                FROM Education 
                WHERE jobseeker_id = :jobseeker_id 
                ORDER BY year_graduated DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':jobseeker_id' => $jobseekerId]);
        return $stmt->fetchAll();
    }

    // Fetch 1:N Work Experience
    public function getSeekerExperience($jobseekerId)
    {
        $sql = "SELECT job_title, company_name, start_date, end_date, job_description 
                FROM Work_Experience 
                WHERE jobseeker_id = :jobseeker_id 
                ORDER BY start_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':jobseeker_id' => $jobseekerId]);
        return $stmt->fetchAll();
    }

    // Securely update the application status
    public function updateApplicationStatus($applicationId, $employerId, $newStatus)
    {
        // We JOIN Job_Postings again in the UPDATE statement to enforce IDOR protection on writes!
        $sql = "UPDATE Applications a
                JOIN Job_Postings jp ON a.job_id = jp.job_id
                SET a.application_status = :status
                WHERE a.application_id = :app_id AND jp.employer_id = :employer_id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':status' => $newStatus,
            ':app_id' => $applicationId,
            ':employer_id' => $employerId
        ]);
    }
}