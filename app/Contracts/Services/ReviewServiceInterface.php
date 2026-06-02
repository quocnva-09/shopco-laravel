<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\DTOs\Review\ReviewApproveDTO;
use App\DTOs\Review\ReviewDTO;
use App\DTOs\Review\ReviewFilterDTO;
use App\Models\Review;
use Illuminate\Pagination\LengthAwarePaginator;

interface ReviewServiceInterface
{
    public function getList(ReviewFilterDTO $filterDTO): LengthAwarePaginator;

    public function findById(int $id): ?Review;

    public function create(ReviewDTO $dto): Review;

    public function approve(Review $review, ReviewApproveDTO $dto): bool;

    public function delete(Review $review): bool;
}
