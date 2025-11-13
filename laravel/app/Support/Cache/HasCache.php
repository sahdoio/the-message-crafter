<?php

namespace App\Support\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

trait HasCache
{
    const string CACHE_PREFIX = 'system-cache-';
    const int CACHE_TIME = 86400; // 24 hours in seconds

    public function hasCache(string $identifier): bool
    {
        if (config('cache.ignore_cache', false)) {
            Log::info('Cache ignored due to configuration');
            return true;
        }

        $cacheKey = self::CACHE_PREFIX . $identifier;
        $cacheValue = Cache::get($cacheKey);

        if ($cacheValue) {
            return false;
        }

        Cache::put($cacheKey, true, self::CACHE_TIME);

        return true;
    }
}
