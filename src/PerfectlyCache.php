<?php
/**
 * Created by PhpStorm.
 * User: Musa
 * Date: 23.03.2019
 * Time: 15:47
 */

namespace Whtht\PerfectlyCache;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Whtht\PerfectlyCache\Builders\QueryBuilder;

class PerfectlyCache
{
    /**
     * @var int
     */
    public static $defaultCacheMinutes = 30;

    /**
     * @var int
     */
    protected static $cacheMultiplier = 60;

    /**
     * @param string $sql
     * @param array $bindings
     * @return string|string[]|null
     */
    public static function mergeBindings(string $sql, array $bindings) {
        foreach($bindings as $binding)
        {
            $value = is_numeric($binding) ? $binding : "'".$binding."'";
            $sql = preg_replace('/\?/', $value, $sql, 1);
        }
        return $sql;
    }

    /**
     * @param QueryBuilder|Model $instance
     * @return string
     */
    public static function generateCacheKey(string $table, string $sql, array $bindings = [], int $minutes = 0) {

        $bindedSql = self::mergeBindings($sql, $bindings);

        $sql = md5($bindedSql);

        return "{$table}_-_{$sql}_-_{$minutes}";
    }

    /**
     * @return int
     */
    public static function getCacheMultiplier() :int {
        return self::$cacheMultiplier;
    }

    /**
     * @param int $cacheMinutes
     * @return float|int
     */
    public static function calcultateCacheMinutes(int $cacheMinutes) :int {
        return self::getCacheMultiplier() * $cacheMinutes;
    }
    /**
     * @param array|string $table
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public static function clearCacheByTable(...$tables) {
        $tables = collect($tables)->flatten()->toArray();
        $keys = Cache::get("perfectly_cache_keys", []);

        $keysToBeForget = array_filter($keys, function ($key) use ($tables) {
           $explode = explode('_-_', $key);
           $tableName = $explode[0];
           return in_array($tableName, $tables);
        });

        foreach ($keysToBeForget as $key) {
            Cache::forget($key);
        }

        $indexes = array_filter(Cache::get("perfectly_cache_keys", []), function ($value) use ($keysToBeForget) {
            return ! in_array($value, $keysToBeForget);
        });

        Cache::forever("perfectly_cache_keys", array_values(array_unique($indexes)));

        return count($keysToBeForget);
    }

    public static function clearAllCaches() {

        $keys = Cache::get("perfectly_cache_keys", []);

        $total = count($keys);
        Cache::forget("perfectly_cache_keys");
        foreach ($keys as $key) {
            Cache::forget($key);
        }

        return $total;
    }

}
