<?php

class Admin
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getSystemOverview()
    {
        $sql = "SELECT role, COUNT(user_id) as total FROM users GROUP BY role";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getSeekersByBarangay()
    {
        $sql = "SELECT b.barangay_name, COUNT(js.jobseeker_id) as seeker_count
                FROM job_seekers js
                JOIN barangays b ON js.barangay_id = b.barangay_id
                GROUP BY js.barangay_id
                ORDER BY seeker_count DESC LIMIT 10";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getTopDemandSkills()
    {
        $sql = "SELECT ms.skill_name, COUNT(jrs.skill_id) as demand_count
                FROM job_required_skills jrs
                JOIN master_skills ms ON jrs.skill_id = ms.skill_id
                GROUP BY jrs.skill_id
                ORDER BY demand_count DESC LIMIT 5";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getPendingEmployers()
    {
        $sql = "SELECT * FROM employers WHERE verified_status = 'Pending' ORDER BY employer_id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // NEW: Fetch Custom Skills entered by users that need approval
    public function getPendingSkills()
    {
        $sql = "SELECT * FROM master_skills WHERE status = 'pending' ORDER BY skill_name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateEmployerVerification($employerId, $status)
    {
        $sql = "UPDATE employers SET verified_status = :status WHERE employer_id = :employer_id";
        return $this->db->prepare($sql)->execute([':status' => $status, ':employer_id' => $employerId]);
    }

    public function updateSkillStatus($skillId, $status)
    {
        $sql = "UPDATE master_skills SET status = :status WHERE skill_id = :skill_id";
        return $this->db->prepare($sql)->execute([':status' => $status, ':skill_id' => $skillId]);
    }

    public function deleteSkill($skillId)
    {
        return $this->db->prepare("DELETE FROM master_skills WHERE skill_id = ?")->execute([$skillId]);
    }
}