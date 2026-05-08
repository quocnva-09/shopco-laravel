<?php

namespace App\DTOs;

use Illuminate\Foundation\Http\FormRequest;

readonly class AddToCartDTO
{
    public function __construct(
        public int $product_id,
        public int $quantity,
    ) {}

    /**
     * Khởi tạo DTO từ Form Request
     */
    public static function fromRequest(FormRequest $request): self
    {
        return new self(
            product_id: (int) $request->validated('product_id'),
            quantity: (int) $request->validated('quantity'),
        );
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
        ];
    }
}
