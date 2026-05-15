<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Models\Review;
use Illuminate\Pagination\LengthAwarePaginator;
use App\DTOs\Review\ReviewFilterDTO;

interface ReviewRepositoryInterface
{
    public function getList(ReviewFilterDTO $filterDTO): LengthAwarePaginator;

    public function getApprovedByProduct(int $productId, ReviewFilterDTO $filterDTO): LengthAwarePaginator;

    public function findById(int $id): ?Review;

    public function create(array $data): Review;

    public function update(Review $review, array $data): bool;

    public function delete(Review $review): bool;
}
