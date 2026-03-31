<?php

class User
{
    private $db;

    public function __construct()
    {
        // Grab the existing PDO connection
        $this->db = Database::getInstance()->getConnection();
    }

    // Secure Registration using Prepared Statements
    public function register($username, $email, $password, $role)
    {
        // Hash the password with bcrypt
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO Users (username, email, password_hash, role, account_status) 
                VALUES (:username, :email, :password_hash, :role, 'Pending')";

        $stmt = $this->db->prepare($sql);

        // Bind parameters safely
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password_hash', $password_hash);
        $stmt->bindParam(':role', $role);

        try {
            return $stmt->execute();
        }
        catch (PDOException $e) {
            // Log error internally, do not show to user
            return false;
        }
    }

    // Secure Login Verification
    public function login($username, $password)
    {
        $sql = "SELECT * FROM Users WHERE username = :username LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $user = $stmt->fetch();

        // Verify the password against the stored bcrypt hash
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }

        return false;
    }
}