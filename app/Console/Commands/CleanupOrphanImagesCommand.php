<?php

namespace App\Console\Commands;

use App\Models\ProductImage;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

#[Signature('app:cleanup-orphan-images')]
#[Description('Clean up orphan images in storage that are not linked to any product')]
class CleanupOrphanImagesCommand extends Command
{

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting orphan image cleanup...');

        $disk = config('filesystems.default'); // Matching the default disk used in FileUploadService
        $folder = 'products';

        // 1. Get all image paths from database
        $dbImages = ProductImage::pluck('img_path')->toArray();
        $dbImagesSet = array_flip($dbImages);

        // 2. Iterate over files in storage
        $files = Storage::disk($disk)->allFiles($folder);

        $deletedCount = 0;
        $now = now();

        foreach ($files as $file) {
            // Check if the file is in the database
            if (!isset($dbImagesSet[$file])) {
                // If not in database, check its age to avoid deleting newly uploaded files
                // that haven't been saved to DB yet
                $lastModified = Storage::disk($disk)->lastModified($file);
                $ageInHours = $now->diffInHours(Carbon::createFromTimestamp($lastModified));

                // Delete if older than 24 hours
                if ($ageInHours >= 24) {
                    Storage::disk($disk)->delete($file);
                    $this->info("Deleted orphan file: {$file}");
                    $deletedCount++;
                }
            }
        }

        $this->info("Cleanup completed. Deleted {$deletedCount} orphan images.");

        return self::SUCCESS;
    }
}
