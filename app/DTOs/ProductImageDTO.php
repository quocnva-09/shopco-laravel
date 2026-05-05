<?php

namespace App\DTOs;

use Illuminate\Foundation\Http\FormRequest;

readonly class ProductImageDTO
{
    public function __construct(
        // TODO: Khai báo các thuộc tính (properties) tại đây. Ví dụ:
        // public int $id,
        // public string $name,
    ) {}

    /**
     * Khởi tạo DTO từ Form Request
     */
    public static function fromRequest(FormRequest $request): self
    {
        return new self(
            // TODO: Map dữ liệu từ request. Ví dụ:
            // id: $request->validated('id'),
            // name: $request->validated('name'),
        );
    }
}