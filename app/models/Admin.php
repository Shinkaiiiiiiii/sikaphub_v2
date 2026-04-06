<?php

class Admin
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Metric 1: Count total registered users by role
    public function getSystemOverview()
    {
        $sql = "SELECT role, COUNT(user_id) as total 
                FROM Users 
                GROUP BY role";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Metric 2: Geographic Breakdown (Seekers per Guimba Barangay)
    public function getSeekersByBarangay()
    {
        $sql = "SELECT b.barangay_name, COUNT(js.jobseeker_id) as seeker_count
                FROM Job_Seekers js
                JOIN Barangays b ON js.barangay_id = b.barangay_id
                GROUP BY js.barangay_id
                ORDER BY seeker_count DESC
                LIMIT 10"; // Top 10 barangays
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Metric 3: Top 5 In-Demand Skills (Based on Employer Job Postings)
    public function getTopDemandSkills()
    {
        $sql = "SELECT ms.skill_name, COUNT(jrs.skill_id) as demand_count
                FROM Job_Required_Skills jrs
                JOIN Master_Skills ms ON jrs.skill_id = ms.skill_id
                GROUP BY jrs.skill_id
                ORDER BY demand_count DESC
                LIMIT 5";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Fetch the queue of unverified employers
    public function getPendingEmployers()
    {
        $sql = "SELECT 
                    e.employer_id, 
                    e.company_name, 
                    e.contact_person, 
                    e.company_email, 
                    e.company_phone, 
                    e.business_permit, 
                    e.verified_status
                FROM Employers e
                WHERE e.verified_status = 'Pending'
                ORDER BY e.employer_id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Securely update the employer's verification status
    public function updateEmployerVerification($employerId, $status)
    {
        $sql = "UPDATE Employers SET verified_status = :status WHERE employer_id = :employer_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':status' => $status,
            ':employer_id' => $employerId
        ]);
    }
}