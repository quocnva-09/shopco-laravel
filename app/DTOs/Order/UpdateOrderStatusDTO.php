<?php

declare(strict_types=1);

namespace App\DTOs\Order;

use App\Enums\OrderStatus;
use Illuminate\Http\Request;

readonly class UpdateOrderStatusDTO
{
    public function __construct(
        public OrderStatus $status
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            status: OrderStatus::from($request->input('status'))
        );
    }
}
