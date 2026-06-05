<?php

declare(strict_types=1);

namespace App\DTOs\Order;

use App\Http\Requests\Order\GuestCheckoutRequest;

readonly class GuestCheckoutDTO
{
    /**
     * @param array<int, array{
     *     product_id: int,
     *     product_variant_id: int|null,
     *     color_id: int|null,
     *     size_id: int|null,
     *     quantity: int
     * }> $items
     */
    public function __construct(
        public array $items,
        public float $deliveryFee,
        public float $discount,
        public ?string $guestName,
        public ?string $guestPhone,
        public ?string $guestEmail,
        public ?string $guestAddress,
    ) {}

    public static function fromRequest(GuestCheckoutRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            items: $validated['items'],
            deliveryFee: (float) ($validated['delivery_fee'] ?? 0),
            discount: (float) ($validated['discount'] ?? 0),
            guestName: $validated['guest_name'] ?? null,
            guestPhone: $validated['guest_phone'] ?? null,
            guestEmail: $validated['guest_email'] ?? null,
            guestAddress: $validated['guest_address'] ?? null,
        );
    }
}
