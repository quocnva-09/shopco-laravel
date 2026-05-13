<?php

namespace App\Models;

use App\Enums\CacheConstants;
use App\Helpers\CacheHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'price',
        'price_discount',
        'description',
        'category_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public static function booted()
    {
        static::forceDeleting(function ($product) {
            foreach ($product->images as $image) {
                if ($image->img_path) {
                    Storage::delete($image->img_path);
                }
            }
        });

        static::deleting(function ($product) {
            $product->cartItems()->delete();
        });

        $clearCache = function () {
            CacheHelper::flushTags([CacheConstants::PRODUCT_TAGS->value]);
        };

        static::created($clearCache);
        static::updated($clearCache);
        static::deleted($clearCache);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
