<?php

class Onboarding
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Fetch barangays to populate the dropdown menu dynamically
    public function getBarangays()
    {
        $sql = "SELECT barangay_id, barangay_name FROM Barangays ORDER BY barangay_name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function createJobSeekerProfile($userId, $data)
    {
        try {
            $this->db->beginTransaction();

            // 1. Insert the mandatory Job Seeker data
            $sqlProfile = "INSERT INTO Job_Seekers 
                (user_id, first_name, last_name, gender, birthdate, street_address, barangay_id, contact_number) 
                VALUES (:user_id, :first_name, :last_name, :gender, :birthdate, :street_address, :barangay_id, :contact_number)";

            $stmtProfile = $this->db->prepare($sqlProfile);
            $stmtProfile->execute([
                ':user_id' => $userId,
                ':first_name' => $data['first_name'],
                ':last_name' => $data['last_name'],
                ':gender' => $data['gender'],
                ':birthdate' => $data['birthdate'],
                ':street_address' => $data['street_address'],
                ':barangay_id' => $data['barangay_id'],
                ':contact_number' => $data['contact_number']
            ]);

            // 2. Upgrade the User's account status to 'Active'
            $sqlStatus = "UPDATE Users SET account_status = 'Active' WHERE user_id = :user_id";
            $stmtStatus = $this->db->prepare($sqlStatus);
            $stmtStatus->execute([':user_id' => $userId]);

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Onboarding Transaction Failed: " . $e->getMessage());
            return false;
        }
    }

    public function createEmployerProfile($userId, $data, $businessPermitFilename)
    {
        try {
            $this->db->beginTransaction();

            // 1. Insert the mandatory Employer data
            $sqlProfile = "INSERT INTO Employers 
                (user_id, company_name, contact_person, company_email, company_phone, street_address, barangay_id, business_permit, verified_status) 
                VALUES (:user_id, :company_name, :contact_person, :company_email, :company_phone, :street_address, :barangay_id, :business_permit, 'Pending')";

            $stmtProfile = $this->db->prepare($sqlProfile);
            $stmtProfile->execute([
                ':user_id' => $userId,
                ':company_name' => $data['company_name'],
                ':contact_person' => $data['contact_person'],
                ':company_email' => $data['company_email'],
                ':company_phone' => $data['company_phone'],
                ':street_address' => $data['street_address'],
                ':barangay_id' => $data['barangay_id'],
                ':business_permit' => $businessPermitFilename
            ]);

            // 2. Upgrade the User's account status to 'Active'
            $sqlStatus = "UPDATE Users SET account_status = 'Active' WHERE user_id = :user_id";
            $stmtStatus = $this->db->prepare($sqlStatus);
            $stmtStatus->execute([':user_id' => $userId]);

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Employer Onboarding Transaction Failed: " . $e->getMessage());
            return false;
        }
    }
}