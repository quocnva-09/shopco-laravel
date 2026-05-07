<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    protected $fillable = [
        "product_id",
        "img_path",
        "alt",
        "is_primary",
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public static function booted()
    {
        static::deleted(function ($image) {
            if ($image->img_path) {
                Storage::delete($image->img_path);
            }
        });
    }
}
