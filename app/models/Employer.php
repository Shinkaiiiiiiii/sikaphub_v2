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
        $sql = "SELECT employer_id FROM employers WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $result = $stmt->fetch();
        return $result ? $result['employer_id'] : false;
    }

    // Fetch the full employer row linked to a user_id (used for onboarding gate checks)
    public function findByUserId($userId)
    {
        $sql = "SELECT * FROM employers WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch();
    }

    // Fetch the employer's core profile row (includes verified_status)
    public function getEmployerDetails($employerId)
    {
        $sql = "SELECT * FROM employers WHERE employer_id = :employer_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':employer_id' => $employerId]);
        return $stmt->fetch();
    }

    // Fetch all jobs posted by this employer
    public function getEmployerJobs($employerId)
    {
        $sql = "SELECT job_id, job_title, job_status, date_posted 
                FROM job_postings 
                WHERE employer_id = :employer_id 
                ORDER BY date_posted DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':employer_id' => $employerId]);
        return $stmt->fetchAll();
    }

    // Fetch applicants for a specific job, STRICTLY ordered by AI Match Score
    // SECURITY FIX: JOIN job_postings to enforce employer ownership; prevents IDOR where
    // an attacker passes an arbitrary job_id to view another company's applicants.
    public function getRankedApplicantsForJob($jobId, $employerId)
    {
        $sql = "SELECT 
                    a.application_id, 
                    a.ai_match_score, 
                    a.application_status, 
                    a.application_date,
                    js.first_name, 
                    js.last_name, 
                    js.contact_number
                FROM applications a
                JOIN job_postings jp ON a.job_id = jp.job_id
                JOIN job_seekers js ON a.jobseeker_id = js.jobseeker_id
                WHERE a.job_id = :job_id
                  AND jp.employer_id = :employer_id
                ORDER BY a.ai_match_score DESC, a.application_date ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':job_id'      => $jobId,
            ':employer_id' => $employerId
        ]);
        return $stmt->fetchAll();
    }

    // Fetch core application and seeker profile, strictly gated by Employer ID
    public function getApplicationDetails($applicationId, $employerId)
    {
        $sql = "SELECT 
                    a.application_id, a.jobseeker_id, a.ai_match_score, a.application_status, a.application_date,
                    js.first_name, js.last_name, js.gender, js.contact_number, js.street_address,
                    js.profile_photo, js.resume_file,
                    m.municipality_name,
                    jp.job_title
                FROM applications a
                JOIN job_postings jp ON a.job_id = jp.job_id
                JOIN job_seekers js ON a.jobseeker_id = js.jobseeker_id
                LEFT JOIN lib_municipalities m ON js.home_municipality_id = m.municipality_id
                WHERE a.application_id = :app_id AND jp.employer_id = :employer_id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':app_id' => $applicationId,
            ':employer_id' => $employerId
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Fetch 1:N Education History
    public function getSeekerEducation($jobseekerId)
    {
        $sql = "SELECT degree_level, school_name, year_graduated 
                FROM education 
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
                FROM work_experience 
                WHERE jobseeker_id = :jobseeker_id 
                ORDER BY start_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':jobseeker_id' => $jobseekerId]);
        return $stmt->fetchAll();
    }

    // Securely update the application status
    public function updateApplicationStatus($applicationId, $employerId, $newStatus)
    {
        // We JOIN job_postings again in the UPDATE statement to enforce IDOR protection on writes!
        $sql = "UPDATE applications a
                JOIN job_postings jp ON a.job_id = jp.job_id
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