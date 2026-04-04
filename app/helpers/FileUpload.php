<?php

class FileUpload
{

    public static function secureUpload($fileArray, $destinationPath, $allowedMimeTypes = ['application/pdf', 'image/jpeg', 'image/png'], $maxSizeMB = 5)
    {

        // 1. Check for basic upload errors
        if (!isset($fileArray['error']) || is_array($fileArray['error'])) {
            throw new RuntimeException("Invalid parameters.");
        }
        if ($fileArray['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException("File upload failed with error code: " . $fileArray['error']);
        }

        // 2. Enforce File Size Limit
        $maxBytes = $maxSizeMB * 1024 * 1024;
        if ($fileArray['size'] > $maxBytes) {
            throw new RuntimeException("File exceeds the maximum limit of {$maxSizeMB}MB.");
        }

        // 3. Cryptographic MIME Type Validation (Read the Magic Bytes)
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($fileArray['tmp_name']);

        if (!in_array($mimeType, $allowedMimeTypes)) {
            throw new RuntimeException("Invalid file format. Only PDF, JPG, and PNG are allowed.");
        }

        // 4. Generate a secure, randomized filename
        $extension = pathinfo($fileArray['name'], PATHINFO_EXTENSION);
        $secureFileName = bin2hex(random_bytes(16)) . '.' . $extension;
        $targetPath = $destinationPath . $secureFileName;

        // 5. Move the file from the temporary buffer to the isolated storage vault
        if (!move_uploaded_file($fileArray['tmp_name'], $targetPath)) {
            throw new RuntimeException("Failed to move uploaded file to secure storage.");
        }

        // Return the secure filename to be saved in the database
        return $secureFileName;
    }
}