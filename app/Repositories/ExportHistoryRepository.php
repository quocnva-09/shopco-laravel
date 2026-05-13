<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\ExportHistoryRepositoryInterface;
use App\Models\ExportHistory;
use Illuminate\Pagination\LengthAwarePaginator;

class ExportHistoryRepository implements ExportHistoryRepositoryInterface
{
    public function create(array $data): ExportHistory
    {
        return ExportHistory::create($data);
    }

    public function paginateByUser(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return ExportHistory::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findByIdAndUser(int $id, int $userId): ExportHistory
    {
        return ExportHistory::where('user_id', $userId)->findOrFail($id);
    }
}
