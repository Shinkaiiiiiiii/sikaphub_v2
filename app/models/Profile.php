<?php

class Profile
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function fetchAllMasterSkills()
    {
        $sql = "SELECT * FROM Master_Skills ORDER BY skill_name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateJobSeekerSkills($jobseekerId, $skillIds, $proficiencyLevel = 'Intermediate')
    {
        try {
            // 1. Lock the database state
            $this->db->beginTransaction();

            // 2. Wipe existing skills to prevent duplicates
            $deleteSql = "DELETE FROM JobSeeker_Skills WHERE jobseeker_id = :jobseeker_id";
            $deleteStmt = $this->db->prepare($deleteSql);
            $deleteStmt->bindParam(':jobseeker_id', $jobseekerId);
            $deleteStmt->execute();

            // 3. Prepare the insertion query once
            $insertSql = "INSERT INTO JobSeeker_Skills (jobseeker_id, skill_id, proficiency_level) 
                          VALUES (:jobseeker_id, :skill_id, :proficiency_level)";
            $insertStmt = $this->db->prepare($insertSql);

            // 4. Loop through the array and insert securely
            foreach ($skillIds as $skillId) {
                $insertStmt->bindValue(':jobseeker_id', $jobseekerId);
                $insertStmt->bindValue(':skill_id', $skillId);
                $insertStmt->bindValue(':proficiency_level', $proficiencyLevel);
                $insertStmt->execute();
            }

            // 5. If everything succeeds, commit to the database permanently
            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            // If any query fails, revert all changes
            $this->db->rollBack();
            error_log("Transaction Failed: " . $e->getMessage());
            return false;
        }
    }
}