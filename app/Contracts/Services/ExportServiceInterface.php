<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\DTOs\Export\ExportDTO;
use App\Models\ExportHistory;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

interface ExportServiceInterface
{
    /**
     * Request an export for products.
     *
     * @param ExportDTO $dto
     * @return ExportHistory
     */
    public function requestProductExport(ExportDTO $dto): ExportHistory;

    /**
     * Get paginated export histories for the authenticated user.
     *
     * @return LengthAwarePaginator
     */
    public function getUserExportHistories(): LengthAwarePaginator;

    /**
     * Get a specific export history for the authenticated user.
     *
     * @param int $id
     * @return ExportHistory
     */
    public function getUserExportHistory(int $id): ExportHistory;

    /**
     * Get the download response for a specific export history.
     *
     * @param int $id
     * @return BinaryFileResponse
     */
    public function downloadProductExport(int $id): BinaryFileResponse;
}
