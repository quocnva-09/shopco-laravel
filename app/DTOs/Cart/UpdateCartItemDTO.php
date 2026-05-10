<?php

declare(strict_types=1);

namespace App\DTOs\Cart;

use Illuminate\Foundation\Http\FormRequest;

readonly class UpdateCartItemDTO
{
    public function __construct(
        public int $quantity,
    ) {}

    /**
     * Khởi tạo DTO từ Form Request
     */
    public static function fromRequest(FormRequest $request): self
    {
        return new self(
            quantity: (int) $request->validated('quantity'),
        );
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'quantity' => $this->quantity,
        ];
    }
}
