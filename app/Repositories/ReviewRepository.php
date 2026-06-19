<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\ReviewRepositoryInterface;
use App\Models\Review;
use Illuminate\Pagination\LengthAwarePaginator;
use App\DTOs\Review\ReviewFilterDTO;

class ReviewRepository implements ReviewRepositoryInterface
{
    public function getList(ReviewFilterDTO $filterDTO): LengthAwarePaginator
    {
        $query = Review::query()->with(['user', 'product']);

        if ($filterDTO->productId !== null) {
            $query->where('product_id', $filterDTO->productId);
        }

        if ($filterDTO->keyword) {
            $query->where('comment', 'like', '%' . $filterDTO->keyword . '%');
        }

        if ($filterDTO->status !== null) {
            $query->where('status', $filterDTO->status);
        }

        if ($filterDTO->rating !== null) {
            $query->where('rating', $filterDTO->rating);
        }

        if ($filterDTO->sortBy && $filterDTO->sortDir) {
            $query->orderBy($filterDTO->sortBy, $filterDTO->sortDir);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($filterDTO->limit);
    }

    public function findById(int $id): ?Review
    {
        return Review::with(['user', 'product'])->find($id);
    }

    public function create(array $data): Review
    {
        return Review::create($data);
    }

    public function update(Review $review, array $data): bool
    {
        return $review->update($data);
    }

    public function delete(Review $review): bool
    {
        return $review->delete();
    }
}
