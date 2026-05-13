<?php

namespace App\Models;

use App\Enums\CacheConstants;
use App\Helpers\CacheHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'slug',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    protected static function booted()
    {
        $clearCache = function () {
            CacheHelper::flushTags([CacheConstants::CATEGORY_TAGS->value]);
        };

        static::created($clearCache);
        static::updated($clearCache);
        static::deleted($clearCache);
    }
}
