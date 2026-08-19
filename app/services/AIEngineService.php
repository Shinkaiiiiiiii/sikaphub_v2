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

        // 3. 3NF LOCATION EXTRACTION: Fetch job's municipality from job_postings
        $sqlJobMunicipality = "SELECT municipality_id FROM job_postings WHERE job_id = :job_id LIMIT 1";
        $stmtJobMunicipality = $this->db->prepare($sqlJobMunicipality);
        $stmtJobMunicipality->execute([':job_id' => $jobId]);
        $jobMunicipalityRow = $stmtJobMunicipality->fetch(PDO::FETCH_ASSOC);
        $jobMunicipalityId = ($jobMunicipalityRow && isset($jobMunicipalityRow['municipality_id'])) 
            ? (int) $jobMunicipalityRow['municipality_id'] 
            : null;

        // 4. 3NF LOCATION EXTRACTION: Fetch seeker's home municipality from job_seekers (FIXED: jobseeker_id column)
        $sqlSeekerHome = "SELECT home_municipality_id FROM job_seekers WHERE jobseeker_id = :jobseeker_id LIMIT 1";
        $stmtSeekerHome = $this->db->prepare($sqlSeekerHome);
        $stmtSeekerHome->execute([':jobseeker_id' => $jobseekerId]);
        $seekerHomeRow = $stmtSeekerHome->fetch(PDO::FETCH_ASSOC);
        $seekerHomeMunicipalityId = ($seekerHomeRow && isset($seekerHomeRow['home_municipality_id'])) 
            ? (int) $seekerHomeRow['home_municipality_id'] 
            : null;

        // 5. 3NF LOCATION EXTRACTION: Fetch seeker's preferred work location municipalities
        $sqlPreferred = "SELECT municipality_id FROM preferred_work_locations WHERE jobseeker_id = :jobseeker_id";
        $stmtPreferred = $this->db->prepare($sqlPreferred);
        $stmtPreferred->execute([':jobseeker_id' => $jobseekerId]);
        $preferredRows = $stmtPreferred->fetchAll(PDO::FETCH_ASSOC);
        $seekerPreferredMunicipalities = array_map(fn($row) => (int) $row['municipality_id'], $preferredRows);

        // 6. Prepare the V2 "Fat Payload" (skills + 3NF location data)
        $data = [
            'job_id'                          => $jobId,
            'jobseeker_id'                    => $jobseekerId,
            'job_skills'                      => $jobSkills,                      // Injects requirement_type (Mandatory/Optional)
            'seeker_skills'                   => $seekerSkills,                   // Injects proficiency_level
            'job_municipality_id'             => $jobMunicipalityId,              // 3NF: job posting's municipality
            'seeker_home_municipality_id'     => $seekerHomeMunicipalityId,       // 3NF: seeker's home municipality (int|null)
            'seeker_preferred_municipalities' => $seekerPreferredMunicipalities    // 3NF: array of preferred municipality IDs
        ];

        // Convert array to a strict JSON string. This exact string will be hashed.
        $jsonPayload = json_encode($data);

        // 7. Cryptography: Generate the HMAC-SHA256 signature
        $signature = hash_hmac('sha256', $jsonPayload, $this->hmacSecret);

        // 8. Initialize the cURL session
        $ch = curl_init($this->apiUrl);

        // 9. Configure the cURL request
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    // Return response as a string
        curl_setopt($ch, CURLOPT_POST, true);              // Set method to POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload); // Attach the raw JSON body

        // Attach the HTTP Headers, injecting our cryptographic signature and S2S Bearer token
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'x-signature: ' . $signature,
            'Authorization: Bearer ' . ($_ENV['AI_API_KEY'] ?? '')
        ]);

        // 10. Execute the request and fetch the HTTP status code
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // 11. Handle cURL network errors
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            return [
                'success' => false,
                'error'   => 'Network error connecting to AI Engine: ' . $error
            ];
        }

        curl_close($ch);

        // 12. Process the Python response
        $decodedResponse = json_decode($response, true);

        if ($httpCode === 200) {
            return [
                'success' => true,
                'data'    => $decodedResponse
            ];
        } else {
            return [
                'success' => false,
                'error'   => 'AI Engine rejected request. HTTP Code: ' . $httpCode,
                'details' => $decodedResponse
            ];
        }
    }
}