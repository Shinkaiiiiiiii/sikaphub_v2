<?php

class AIEngineService
{
    private $apiUrl;
    private $hmacSecret;
    private $db;

    public function __construct()
    {
        // Load configurations from the Front Controller's parsed environment
        $this->apiUrl = $_ENV['AI_ENGINE_URL'];
        $this->hmacSecret = $_ENV['HMAC_SECRET'];

        // V2 UPGRADE: Inject the Database connection to build Fat Payloads
        $this->db = Database::getInstance()->getConnection();
    }

    public function triggerMatchComputation($jobId, $jobseekerId)
    {
        // 1. V2 DATA EXTRACTION: Fetch Job Requirements
        $sqlJob = "SELECT skill_id, requirement_type FROM job_required_skills WHERE job_id = :job_id";
        $stmtJob = $this->db->prepare($sqlJob);
        $stmtJob->execute([':job_id' => $jobId]);
        $jobSkills = $stmtJob->fetchAll(PDO::FETCH_ASSOC);

        // 2. V2 DATA EXTRACTION: Fetch Seeker Capabilities
        $sqlSeeker = "SELECT skill_id, proficiency_level FROM jobseeker_skills WHERE jobseeker_id = :jobseeker_id";
        $stmtSeeker = $this->db->prepare($sqlSeeker);
        $stmtSeeker->execute([':jobseeker_id' => $jobseekerId]);
        $seekerSkills = $stmtSeeker->fetchAll(PDO::FETCH_ASSOC);

        // 3. Prepare the V2 "Fat Payload"
        $data = [
            'job_id' => $jobId,
            'jobseeker_id' => $jobseekerId,
            'job_skills' => $jobSkills,     // Injects requirement_type (Mandatory/Optional)
            'seeker_skills' => $seekerSkills // Injects proficiency_level
        ];

        // Convert array to a strict JSON string. This exact string will be hashed.
        $jsonPayload = json_encode($data);

        // 4. Cryptography: Generate the HMAC-SHA256 signature
        $signature = hash_hmac('sha256', $jsonPayload, $this->hmacSecret);

        // 5. Initialize the cURL session
        $ch = curl_init($this->apiUrl);

        // 6. Configure the cURL request
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
        curl_setopt($ch, CURLOPT_POST, true);           // Set method to POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload); // Attach the raw JSON body

        // Attach the HTTP Headers, injecting our cryptographic signature
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'x-signature: ' . $signature
        ]);

        // 7. Execute the request and fetch the HTTP status code
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // 8. Handle cURL network errors
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            return [
                'success' => false,
                'error' => 'Network error connecting to AI Engine: ' . $error
            ];
        }

        curl_close($ch);

        // 9. Process the Python response
        $decodedResponse = json_decode($response, true);

        if ($httpCode === 200) {
            return [
                'success' => true,
                'data' => $decodedResponse
            ];
        } else {
            return [
                'success' => false,
                'error' => 'AI Engine rejected request. HTTP Code: ' . $httpCode,
                'details' => $decodedResponse
            ];
        }
    }
}