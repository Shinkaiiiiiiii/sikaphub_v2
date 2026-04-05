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
}