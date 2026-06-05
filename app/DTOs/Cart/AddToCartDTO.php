<?php

namespace App\DTOs\Cart;

use Illuminate\Foundation\Http\FormRequest;

readonly class AddToCartDTO
{
    public function __construct(
        public int $product_id,
        public int $quantity,
        public ?int $product_variant_id = null,
        public ?int $color_id = null,
        public ?int $size_id = null,
    ) {}

    /**
     * Khởi tạo DTO từ Form Request.
     * Hỗ trợ 2 chế độ:
     *   - Mode 1: client gửi product_variant_id trực tiếp
     *   - Mode 2: client gửi color_id, size_id → CartService sẽ resolve sang product_variant_id
     */
    public static function fromRequest(FormRequest $request): self
    {
        return new self(
            product_id: (int) $request->validated('product_id'),
            quantity: (int) $request->validated('quantity'),
            product_variant_id: $request->validated('product_variant_id')
                ? (int) $request->validated('product_variant_id')
                : null,
            color_id: $request->validated('color_id')
                ? (int) $request->validated('color_id')
                : null,
            size_id: $request->validated('size_id')
                ? (int) $request->validated('size_id')
                : null,
        );
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'product_id'         => $this->product_id,
            'quantity'           => $this->quantity,
            'product_variant_id' => $this->product_variant_id,
            'color_id'           => $this->color_id,
            'size_id'            => $this->size_id,
        ];
    }
}
