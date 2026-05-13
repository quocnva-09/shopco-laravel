<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Models\ExportHistory;
use Illuminate\Pagination\LengthAwarePaginator;

interface ExportHistoryRepositoryInterface
{
    public function create(array $data): ExportHistory;

    public function paginateByUser(int $userId, int $perPage = 10): LengthAwarePaginator;

    public function findByIdAndUser(int $id, int $userId): ExportHistory;
}
