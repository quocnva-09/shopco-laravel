<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\DTOs\Export\ExportDTO;
use App\Models\ExportHistory;
use Illuminate\Pagination\LengthAwarePaginator;

interface ExportServiceInterface
{
    /**
     * Request an export for products.
     */
    public function requestProductExport(ExportDTO $dto): ExportHistory;

    /**
     * Get paginated export histories for the authenticated user.
     */
    public function getUserExportHistories(): LengthAwarePaginator;

    /**
     * Get a specific export history for the authenticated user.
     */
    public function getUserExportHistory(int $id): ExportHistory;

    /**
     * Get the download url for a specific export history.
     */
    public function getProductExportUrl(int $id): string;
}
