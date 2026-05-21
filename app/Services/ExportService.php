<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Repositories\ExportHistoryRepositoryInterface;
use App\Contracts\Services\ExportServiceInterface;
use App\DTOs\Export\ExportDTO;
use App\Enums\ExportStatus;
use App\Jobs\ProcessProductExport;
use App\Models\ExportHistory;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use LogicException;


class ExportService implements ExportServiceInterface
{
    public function __construct(
        protected readonly ExportHistoryRepositoryInterface $exportHistoryRepo,
    ) {
    }

    public function requestProductExport(ExportDTO $dto): ExportHistory
    {
        $exportHistory = $this->exportHistoryRepo->create([
            'user_id' => Auth::id(),
            'type' => 'products',
            'format' => $dto->format,
            'status' => ExportStatus::PENDING,
        ]);

        ProcessProductExport::dispatch($exportHistory, $dto->filters);

        return $exportHistory;
    }

    public function getUserExportHistories(): LengthAwarePaginator
    {
        return $this->exportHistoryRepo->paginateByUser((int) Auth::id());
    }

    public function getUserExportHistory(int $id): ExportHistory
    {
        return $this->exportHistoryRepo->findByIdAndUser($id, (int) Auth::id());
    }

    public function getProductExportUrl(int $id): string
    {
        $exportHistory = $this->getUserExportHistory($id);

        if (!$exportHistory->status->isFinalState() || $exportHistory->status !== ExportStatus::COMPLETED) {
            throw new LogicException('Export is not ready for download.');
        }

        if (!$exportHistory->file_path || !Storage::disk('s3')->exists($exportHistory->file_path)) {
            throw new LogicException('Export file not found.');
        }

        return Storage::disk('s3')->temporaryUrl(
            $exportHistory->file_path,
            now()->addMinutes(30)
        );
    }
}
