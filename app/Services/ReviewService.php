<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Repositories\ReviewRepositoryInterface;
use App\Contracts\Services\ReviewServiceInterface;
use App\DTOs\Review\ReviewApproveDTO;
use App\DTOs\Review\ReviewDTO;
use App\DTOs\Review\ReviewFilterDTO;
use App\Enums\OrderStatus;
use App\Models\OrderItem;
use App\Models\Review;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ReviewService implements ReviewServiceInterface
{
    public function __construct(
        protected ReviewRepositoryInterface $repository
    ) {
    }

    public function getList(ReviewFilterDTO $filterDTO): LengthAwarePaginator
    {
        return $this->repository->getList($filterDTO);
    }

    public function findById(int $id): ?Review
    {
        return $this->repository->findById($id);
    }

    public function create(ReviewDTO $dto): Review
    {
        $hasPaidOrder = OrderItem::query()
            ->where('product_id', $dto->productId)
            ->whereHas('order', function ($q) use ($dto) {
                $q->where('user_id', $dto->userId)
                  ->where('status', OrderStatus::PAID);
            })
            ->where('id', $dto->orderItemId)
            ->exists();

        if (!$hasPaidOrder) {
            throw new AccessDeniedHttpException('You can only review products you have purchased and paid for.');
        }

        return $this->repository->create($dto->toArray());
    }

    public function approve(Review $review, ReviewApproveDTO $dto): bool
    {
        return $this->repository->update($review, ['is_approved' => $dto->isApproved]);
    }

    public function delete(Review $review): bool
    {
        return $this->repository->delete($review);
    }
}
