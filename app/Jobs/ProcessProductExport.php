<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\ExportStatus;
use App\Exports\ProductExport;
use App\Models\ExportHistory;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class ProcessProductExport implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private ExportHistory $exportHistory;

    private array $filters;

    /**
     * Create a new job instance.
     */
    public function __construct(ExportHistory $exportHistory, array $filters = [])
    {
        $this->exportHistory = $exportHistory;
        $this->filters = $filters;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->exportHistory->transitionTo(ExportStatus::PROCESSING);

        try {
            $fileName = 'export_'.time().'_'.uniqid().'.'.$this->exportHistory->format;
            $filePath = 'exports/'.$fileName;

            // Generate and store the file
            Excel::store(new ProductExport($this->filters), $filePath, 's3');

            $this->exportHistory->transitionTo(ExportStatus::COMPLETED, [
                'file_path' => $filePath,
            ]);
        } catch (Exception $e) {
            $this->exportHistory->transitionTo(ExportStatus::FAILED, [
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}
