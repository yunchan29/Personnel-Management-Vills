<?php

namespace App\Http\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

trait FileValidationTrait
{
    /**
     * Validate file by checking magic bytes
     *
     * @param UploadedFile $file
     * @param array $allowedTypes ['jpeg', 'png', 'gif', 'pdf']
     * @return array ['valid' => bool, 'type' => string|null, 'error' => string|null]
     */
    protected function validateFileByMagicBytes(UploadedFile $file, array $allowedTypes = ['jpeg', 'png', 'gif']): array
    {
        $fileContents = file_get_contents($file->getRealPath());
        $detectedType = null;

        // Check for JPEG
        if (in_array('jpeg', $allowedTypes) && substr($fileContents, 0, 2) === "\xFF\xD8") {
            $detectedType = 'jpeg';
        }
        // Check for PNG
        elseif (in_array('png', $allowedTypes) && substr($fileContents, 0, 8) === "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A") {
            $detectedType = 'png';
        }
        // Check for GIF
        elseif (in_array('gif', $allowedTypes) && (substr($fileContents, 0, 6) === "GIF87a" || substr($fileContents, 0, 6) === "GIF89a")) {
            $detectedType = 'gif';
        }
        // Check for PDF
        elseif (in_array('pdf', $allowedTypes) && substr($fileContents, 0, 4) === '%PDF') {
            $detectedType = 'pdf';
        }

        if ($detectedType === null) {
            $allowedTypesString = implode(', ', array_map('strtoupper', $allowedTypes));
            return [
                'valid' => false,
                'type' => null,
                'error' => "Invalid file type detected. Please upload a valid {$allowedTypesString} file."
            ];
        }

        return [
            'valid' => true,
            'type' => $detectedType,
            'error' => null
        ];
    }

    /**
     * Validate and store file with random name
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param array $allowedTypes
     * @param string $disk
     * @return array ['success' => bool, 'path' => string|null, 'error' => string|null]
     */
    protected function validateAndStoreFile(
        UploadedFile $file,
        string $directory,
        array $allowedTypes = ['jpeg', 'png', 'gif'],
        string $disk = 'public'
    ): array {
        $validation = $this->validateFileByMagicBytes($file, $allowedTypes);

        if (!$validation['valid']) {
            Log::warning('Invalid file upload attempt', [
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
                'directory' => $directory
            ]);

            return [
                'success' => false,
                'path' => null,
                'error' => $validation['error']
            ];
        }

        // Generate random filename with correct extension
        $extension = $validation['type'] === 'jpeg' ? 'jpg' : $validation['type'];
        $randomName = \Str::random(40) . '.' . $extension;
        $path = $file->storeAs($directory, $randomName, $disk);

        Log::info('File uploaded successfully', [
            'path' => $path,
            'type' => $validation['type']
        ]);

        return [
            'success' => true,
            'path' => $path,
            'error' => null
        ];
    }
}
