<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

class FileUploadService
{
    /**
     * Upload a file to storage.
     *
     * @param UploadedFile $file The file to upload
     * @param string $folder The folder to store the file
     * @param string $disk The storage disk (default: 's3')
     * @return string|null The file path, or null if upload fails
     */
    public function upload(UploadedFile $file, string $folder = 'uploads', string $disk = 's3'): ?string
    {
        try {
            return $file->storePublicly($folder, $disk);
        } catch (Exception $e) {
            Log::error('File upload failed: ' . $e->getMessage(), [
                'folder' => $folder,
                'disk' => $disk,
                'file' => $file->getClientOriginalName(),
            ]);

            return null;
        }
    }

    /**
     * Delete a file from storage.
     *
     * @param string $path The file path
     * @param string $disk The storage disk (default: 's3')
     * @return bool True on success, false on failure
     */
    public function delete(string $path, string $disk = 's3'): bool
    {
        try {
            if (Storage::disk($disk)->exists($path)) {
                return Storage::disk($disk)->delete($path);
            }

            return true;
        } catch (Exception $e) {
            Log::error('File deletion failed: ' . $e->getMessage(), [
                'path' => $path,
                'disk' => $disk,
            ]);

            return false;
        }
    }

    /**
     * Get the public URL for a file.
     *
     * @param string $path The file path
     * @param string $disk The storage disk (default: 's3')
     * @return string The public URL
     */
    public function url(string $path, string $disk = 's3'): string
    {
        return Storage::disk($disk)->url($path);
    }
}
