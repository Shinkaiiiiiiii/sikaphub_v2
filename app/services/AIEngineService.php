<?php

class AIEngineService
{
    private $apiUrl;
    private $hmacSecret;

    public function __construct()
    {
        // Load configurations from the Front Controller's parsed environment
        $this->apiUrl = $_ENV['AI_ENGINE_URL'];
        $this->hmacSecret = $_ENV['HMAC_SECRET'];
    }

    public function triggerMatchComputation($jobId, $jobseekerId)
    {
        // 1. Prepare the exact data payload
        $data = [
            'job_id' => $jobId,
            'jobseeker_id' => $jobseekerId
        ];

        // Convert array to a strict JSON string. This exact string will be hashed.
        $jsonPayload = json_encode($data);

        // 2. Cryptography: Generate the HMAC-SHA256 signature
        $signature = hash_hmac('sha256', $jsonPayload, $this->hmacSecret);

        // 3. Initialize the cURL session
        $ch = curl_init($this->apiUrl);

        // 4. Configure the cURL request
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string, don't echo it
        curl_setopt($ch, CURLOPT_POST, true);           // Set method to POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload); // Attach the raw JSON body

        // Attach the HTTP Headers, injecting our cryptographic signature
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'x-signature: ' . $signature
        ]);

        // 5. Execute the request and fetch the HTTP status code
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // 6. Handle cURL network errors (e.g., Python server is offline)
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            return [
                'success' => false,
                'error' => 'Network error connecting to AI Engine: ' . $error
            ];
        }

        curl_close($ch);

        // 7. Process the Python response
        $decodedResponse = json_decode($response, true);

        if ($httpCode === 200) {
            return [
                'success' => true,
                'data' => $decodedResponse
            ];
        } else {
            // Handle 401 Unauthorized or 403 Forbidden from Python
            return [
                'success' => false,
                'error' => 'AI Engine rejected request. HTTP Code: ' . $httpCode,
                'details' => $decodedResponse
            ];
        }
    }
}