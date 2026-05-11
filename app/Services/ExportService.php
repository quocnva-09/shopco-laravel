<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Services\ExportServiceInterface;
use App\DTOs\ExportDTO;
use App\Enums\ExportStatus;
use App\Jobs\ProcessProductExport;
use App\Models\ExportHistory;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportService implements ExportServiceInterface
{
    /**
     * Request an export for products.
     *
     * @param ExportDTO $dto
     * @return ExportHistory
     */
    public function requestProductExport(ExportDTO $dto): ExportHistory
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $exportHistory = ExportHistory::create([
            'user_id' => $user->id,
            'type' => 'products',
            'format' => $dto->format,
            'status' => ExportStatus::PENDING,
        ]);

        ProcessProductExport::dispatch($exportHistory, $dto->filters);

        return $exportHistory;
    }

    /**
     * Get paginated export histories for the authenticated user.
     *
     * @return LengthAwarePaginator
     */
    public function getUserExportHistories(): LengthAwarePaginator
    {
        return ExportHistory::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    /**
     * Get a specific export history for the authenticated user.
     *
     * @param int $id
     * @return ExportHistory
     */
    public function getUserExportHistory(int $id): ExportHistory
    {
        return ExportHistory::where('user_id', Auth::id())->findOrFail($id);
    }

    /**
     * Get the download response for a specific export history.
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \LogicException
     */
    public function downloadProductExport(int $id): BinaryFileResponse
    {
        $exportHistory = $this->getUserExportHistory($id);

        if (!$exportHistory->status->isFinalState() || $exportHistory->status !== ExportStatus::COMPLETED) {
            throw new \LogicException('Export is not ready for download.');
        }

        if (!$exportHistory->file_path || !Storage::disk('local')->exists($exportHistory->file_path)) {
            throw new \LogicException('Export file not found.');
        }

        return response()->download(Storage::disk('local')->path($exportHistory->file_path));
    }
}
