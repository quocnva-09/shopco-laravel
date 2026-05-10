<?php

declare(strict_types=1);

namespace App\DTOs\Order;

class OrderFilterDTO
{
    public function __construct(
        public readonly int $perPage = 15,
        public readonly ?string $search = null,
        public readonly ?string $status = null,
        public readonly string $sortBy = 'created_at',
        public readonly string $sortDir = 'desc',
        public readonly ?int $userId = null
    ) {
    }

    public static function fromRequest(array $data, ?int $userId = null): self
    {
        return new self(
            perPage: isset($data['per_page']) ? (int) $data['per_page'] : 15,
            search: $data['search'] ?? null,
            status: $data['status'] ?? null,
            sortBy: $data['sort_by'] ?? 'created_at',
            sortDir: $data['sort_dir'] ?? 'desc',
            userId: $userId
        );
    }
}
