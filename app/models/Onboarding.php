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

    // Fetch municipalities for the employer onboarding location dropdown
    public function getMunicipalities()
    {
        $sql = "SELECT municipality_id, municipality_name, province_name FROM lib_municipalities ORDER BY municipality_name ASC";
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

    public function createEmployerProfile($userId, $data, $permitFilename)
    {
        try {
            $this->db->beginTransaction();

            // 1. Insert the mandatory Employer data
            $sqlProfile = "INSERT INTO Employers 
                (user_id, company_name, contact_person, company_email, company_phone, street_address, municipality_id, postal_code, business_permit, industry, company_size, company_description, company_logo, verified_status) 
                VALUES (:user_id, :company_name, :contact_person, :company_email, :company_phone, :street_address, :municipality_id, :postal_code, :business_permit, :industry, :company_size, :company_description, :company_logo, :verified_status)";

            $stmtProfile = $this->db->prepare($sqlProfile);
            $stmtProfile->execute([
                ':user_id'             => $userId,
                ':company_name'        => $data['company_name'],
                ':contact_person'      => $data['contact_person'],
                ':company_email'       => $data['company_email'],
                ':company_phone'       => $data['company_phone'],
                ':street_address'      => $data['street_address'],
                ':municipality_id'     => $data['municipality_id'],
                ':postal_code'         => $data['postal_code'],
                ':business_permit'     => $permitFilename,
                ':industry'            => $data['industry'] ?? null,
                ':company_size'        => $data['company_size'] ?? null,
                ':company_description' => $data['company_description'] ?? null,
                ':company_logo'        => $data['company_logo'] ?? null,
                ':verified_status'     => 'Pending'
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
            throw $e;
        }
    }
}