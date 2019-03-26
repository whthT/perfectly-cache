<?php
/**
 * Created by PhpStorm.
 * User: Musa
 * Date: 23.03.2019
 * Time: 15:47
 */

namespace Whtht\PerfectlyCache;


use Whtht\PerfectlyCache\Builders\QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

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
     * @return bool
     */
    public static function gzenabled() :bool {
        return function_exists('gzencode') && function_exists('gzdecode');
    }

    /**
     * @param Collection $data
     * @return string
     */
    public static function compressOutput(Collection $data) :string {

        $data = $data->toJson();
        if (self::gzenabled()) {
            $data = gzencode($data);
        }

        return $data;
    }

    /**
     * @param $data
     * @return Collection
     */
    public static function uncompressOutput($data) {

        if (self::gzenabled() && $data) {
            $data = gzdecode($data);
        }

        return collect(json_decode($data, true));
    }

    /**
     * @param string $table
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public static function clearCacheByTable(array $table) {
        $store = config('perfectly-cache.cache-store', 'perfectly-cache');

        return Cache::store($store)->forgetByTable($table);
    }

    public static function clearAllCaches() {
        $store = config('perfectly-cache.cache-store', 'perfectly-cache');

        return Cache::store($store)->flush();
    }

}
