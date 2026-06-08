<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'color_id',
        'size_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    /**
     * Returns the variant name in "Red / M" format, used to snapshot into order_item.
     */
    public function getVariantNameAttribute(): string
    {
        $parts = array_filter([
            $this->color?->name,
            $this->size?->name,
        ]);

        return implode(' / ', $parts);
    }
}
