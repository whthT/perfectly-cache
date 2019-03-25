<?php
/**
 * Created by PhpStorm.
 * User: Musa
 * Date: 23.03.2019
 * Time: 15:47
 */

namespace Whtht\PerfectlyCache;


use Whtht\PerfectlyCache\Builders\QueryBuilder;
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
     * @param string $sql
     * @return string
     */
    public static function generateCacheKey(QueryBuilder $instance) {
        $bindedSql = self::mergeBindings($instance->toSql(), $instance->getBindings());

        return $instance->from."_".md5($bindedSql).".".$instance->cacheMinutes;
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
    public static function clearCacheByTable(string $table) {
        $store = config('perfectly-cache.cache-store', 'perfectly-cache');

        Cache::store($store)->deleteMultiple([$table]);
    }

    public static function clearAllCaches() {
        $store = config('perfectly-cache.cache-store', 'perfectly-cache');

        Cache::store($store)->forgetAll();
    }

}
