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

    // 🚨 SURGICAL PATCH: 3NF Location Upgrade
    public function getSeekersByMunicipality()
    {
        // Strictly lowercase table names for Linux case-sensitivity constraints
        $sql = "SELECT m.municipality_name, COUNT(js.jobseeker_id) as seeker_count
                FROM job_seekers js
                JOIN lib_municipalities m ON js.home_municipality_id = m.municipality_id
                GROUP BY js.home_municipality_id, m.municipality_name
                ORDER BY seeker_count DESC LIMIT 10";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // 🚨 SURGICAL PATCH: Added ms.skill_name to GROUP BY for STRICT_MODE compliance
    public function getTopDemandSkills()
    {
        $sql = "SELECT ms.skill_name, COUNT(jrs.skill_id) as demand_count
                FROM job_required_skills jrs
                JOIN master_skills ms ON jrs.skill_id = ms.skill_id
                GROUP BY jrs.skill_id, ms.skill_name
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