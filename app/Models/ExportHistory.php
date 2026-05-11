<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ExportStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use LogicException;

class ExportHistory extends Model
{
    use HasFactory;
    use Prunable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'format',
        'status',
        'file_path',
        'error_message',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ExportStatus::class,
        ];
    }

    /**
     * Get the user that owns the export history.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the prunable model query.
     */
    public function prunable(): Builder
    {
        return static::where('created_at', '<=', now()->subDays(7));
    }

    /**
     * Prepare the model for pruning.
     */
    protected function pruning(): void
    {
        if ($this->file_path) {
            Storage::disk('local')->delete($this->file_path);
        }
    }

    /**
     * Transition to a new export status securely.
     *
     * @param ExportStatus $status
     * @param array<string, mixed> $additionalData
     * @throws LogicException
     */
    public function transitionTo(ExportStatus $status, array $additionalData = []): void
    {
        if (!$this->status->canTransitionTo($status)) {
            throw new LogicException("Cannot transition from {$this->status->value} to {$status->value}");
        }

        $this->update(array_merge(['status' => $status], $additionalData));
    }
}
