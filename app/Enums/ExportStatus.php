<?php

declare(strict_types=1);

namespace App\Enums;

enum ExportStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PROCESSING => 'Processing',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::PROCESSING => 'info',
            self::COMPLETED => 'success',
            self::FAILED => 'danger',
        };
    }

    public function isFinalState(): bool
    {
        return match ($this) {
            self::COMPLETED, self::FAILED => true,
            default => false,
        };
    }

    public function canTransitionTo(self $status): bool
    {
        return match ($this) {
            self::PENDING => in_array($status, [self::PROCESSING, self::FAILED], true),
            self::PROCESSING => in_array($status, [self::COMPLETED, self::FAILED], true),
            self::COMPLETED, self::FAILED => false,
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
