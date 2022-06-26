<?php

namespace App\Services;

use Cache;
use Carbon\Carbon;

class CacheUtilService
{
    private const SHORTCUT_REQUEST_COUNTER_KEY = 'shortcut:request:counter';
    private const SHORTCUT_API_TOKENS_KEY = 'shortcut:api:tokens';

    /**
     * @return int|mixed
     */
    public static function getShortcutRequestCounter()
    {
        $cacheKey = self::SHORTCUT_REQUEST_COUNTER_KEY;
        $cache = Cache::get($cacheKey);
        $now = Carbon::now();

        if (!empty($cache)) {
            $isExpired = $cache['created_at'] > $now->subMinutes(2);

            if (!$isExpired) {
                return $cache['counter'];
            }
        }

        $cache = [
            'counter' => 0,
            'created_at' => $now
        ];

        Cache::put($cacheKey, $cache, 2*60); // Store for 2 minutes.

        return 0;
    }

    /**
     * @return Carbon|mixed
     */
    public static function getShortcutRequestExpirationTime()
    {
        $cacheKey = self::SHORTCUT_REQUEST_COUNTER_KEY;
        $cache = Cache::get($cacheKey);

        if (empty($cache)) {
            return 0;
        }

        return $cache['created_at']->diff(Carbon::now());
    }

    /**
     * @param int $counter
     * @return void
     */
    public static function replaceShortcutRequestCounter(int $counter)
    {
        $cacheKey = self::SHORTCUT_REQUEST_COUNTER_KEY;
        $cache = Cache::get($cacheKey);

        $cache['counter'] = $counter;
        $expirationTime = $cache['created_at']->diff(Carbon::now());

        Cache::put($cacheKey, $cache, $expirationTime);
    }

    /**
     * @return mixed
     */
    public static function getShortcutAPITokens()
    {
        $cacheKey = self::SHORTCUT_API_TOKENS_KEY;
        $cache = Cache::get($cacheKey);

        if (!empty($cache)) {
            return $cache;
        }

        $cache = config('shortcut.shortcut_api_tokens') ?? [];

        self::replaceShortcutAPITokens($cache);

        return $cache;
    }

    /**
     * @param array $tokens
     * @return void
     */
    public static function replaceShortcutAPITokens(array $tokens)
    {
        Cache::put(self::SHORTCUT_API_TOKENS_KEY, $tokens, 5*60); // Store for 5 minutes
    }

    /**
     * @return void
     */
    public static function clearShortcutAPITokensCache()
    {
        Cache::forget(self::SHORTCUT_API_TOKENS_KEY);
    }
}
