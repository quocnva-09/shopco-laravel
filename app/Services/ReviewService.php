<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Repositories\ReviewRepositoryInterface;
use App\Contracts\Services\ReviewServiceInterface;
use App\DTOs\Review\GuestReviewDTO;
use App\DTOs\Review\ReviewApproveDTO;
use App\DTOs\Review\ReviewDTO;
use App\DTOs\Review\ReviewFilterDTO;
use App\Enums\OrderStatus;
use App\Mail\ReviewApproved;
use App\Mail\ReviewCreated;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

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
            throw new AccessDeniedHttpException(__('exception.review_unpurchased_product'));
        }

        $review = $this->repository->create($dto->toArray());

        Mail::to($review->user->email)->later(now()->addSeconds(30), new ReviewCreated($review));

        return $review;
    }

    public function createGuestReview(GuestReviewDTO $dto): Review
    {
        // Guard 1: Order must exist
        $order = Order::with('orderItems')->find($dto->orderId);
        if ($order === null) {
            throw new NotFoundHttpException(__('exception.order_not_found'));
        }

        // Guard 2: Order must be in PAID status
        if ($order->status !== OrderStatus::PAID) {
            throw new AccessDeniedHttpException(
                __('exception.guest_review_order_not_paid')
            );
        }

        // Guard 3: Order has not been reviewed yet (anti-spam)
        $alreadyReviewed = Review::whereHas('orderItem', function ($q) use ($dto): void {
            $q->where('order_id', $dto->orderId);
        })->exists();

        if ($alreadyReviewed) {
            throw new ConflictHttpException(
                __('exception.guest_review_order_already_reviewed')
            );
        }

        // Guard 4: Product must belong to the order
        $orderItem = $order->orderItems
            ->where('product_id', $dto->productId)
            ->first();

        if ($orderItem === null) {
            throw new UnprocessableEntityHttpException(
                __('exception.guest_review_product_not_in_order')
            );
        }

        // Resolve order_item_id from order_id + product_id
        $review = $this->repository->create([
            'order_item_id' => $orderItem->id,
            'product_id'    => $dto->productId,
            'user_id'       => null,
            'guest_name'    => $dto->guestName,
            'guest_email'   => $dto->guestEmail,
            'rating'        => $dto->rating,
            'comment'       => $dto->comment,
            'is_approved'   => false,
        ]);

        Mail::to($dto->guestEmail)->later(now()->addSeconds(30), new ReviewCreated($review));

        return $review;
    }

    public function approve(Review $review, ReviewApproveDTO $dto): bool
    {
        $updated = $this->repository->update($review, ['is_approved' => $dto->isApproved]);

        if ($updated) {
            $review->refresh();
            $email = $review->user?->email ?? $review->guest_email;

            if ($email) {
                Mail::to($email)->later(now()->addSeconds(30), new ReviewApproved($review));
            }
        }

        return $updated;
    }

    public function delete(Review $review): bool
    {
        return $this->repository->delete($review);
    }
}
