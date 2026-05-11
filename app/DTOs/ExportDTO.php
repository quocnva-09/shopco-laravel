<?php

declare(strict_types=1);

namespace App\DTOs;

class ExportDTO
{
    public function __construct(
        public readonly string $format,
        public readonly array $filters = []
    ) {
    }

    public static function fromRequest(array $data): self
    {
        $format = $data['format'] ?? 'xlsx';
        unset($data['format']);

        return new self(
            format: $format,
            filters: $data
        );
    }
}
