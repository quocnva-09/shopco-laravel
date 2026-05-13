<?php

declare(strict_types=1);

namespace App\Helpers;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheHelper
{
    public static function remember(string $key, int $ttl, Closure $callback): mixed
    {
        if (Cache::has($key)) {
            Log::info("Cache hit: {$key}");
        } else {
            Log::info("Cache miss: {$key}");
        }

        return Cache::remember($key, $ttl, $callback);
    }

    public static function rememberWithTags(array $tags, string $key, int $ttl, Closure $callback): mixed
    {
        if (Cache::tags($tags)->has($key)) {
            Log::info("Cache hit (tags: " . implode(', ', $tags) . "): {$key}");
        } else {
            Log::info("Cache miss (tags: " . implode(', ', $tags) . "): {$key}");
        }

        return Cache::tags($tags)->remember($key, $ttl, $callback);
    }

    public static function forget(string $key): void
    {
        Cache::forget($key);
        Log::info("Cache forgotten: {$key}");
    }

    public static function flushTags(array $tags): void
    {
        foreach ($tags as $tag) {
            Cache::tags([$tag])->flush();
            Log::info("Cache tag flushed: {$tag}");
        }
    }
}
