<?php
/**
 * Created by PhpStorm.
 * User: Musa
 * Date: 23.03.2019
 * Time: 16:12
 */

namespace Whtht\PerfectlyCache\Builders;


use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Cache;
use Whtht\PerfectlyCache\PerfectlyCache;

class QueryBuilder extends Builder
{
    /**
     * @var bool
     */
    public $isCacheEnable = false;
    public $cacheKey, $cacheMinutes;
    public $cacheSkip = false;

    /**
     * @param array $columns
     * @return \Illuminate\Support\Collection
     */
    public function get($columns = ['*'])
    {
        return $this->rememberProgress($columns);
    }

    /**
     * @return string
     */
    public function getTable() {
        return $this->from;
    }

    /**
     * @return \Illuminate\Config\Repository|mixed
     */
    public function getCacheMinutes() {
        $minutes = $this->cacheMinutes;
        $minutes = $minutes ?: config('perfectly-cache.minutes', PerfectlyCache::$defaultCacheMinutes);

        return $minutes;
    }

    /**
     * The progress for cache remember.
     * Last action of PerfectlyCache
     * @param array $columns
     * @return \Illuminate\Config\Repository|\Illuminate\Support\Collection|mixed
     */
    public function rememberProgress($columns = ["*"]) {

        $cacheEnabled = config('perfectly-cache.enabled', true);

        if ($cacheEnabled && $this->isCacheEnable && ! $this->cacheSkip) {

            $this->cacheKey = PerfectlyCache::generateCacheKey($this->getTable(), $this->toSql(), $this->getBindings(), $this->getCacheMinutes());

            $calculatedCacheMinutes = PerfectlyCache::calcultateCacheMinutes($this->cacheMinutes);
            return Cache::remember($this->cacheKey, $calculatedCacheMinutes, function () use ($columns) {

                $pck = Cache::get("perfectly_cache_keys", []);

                array_push($pck, $this->cacheKey);

                Cache::forever("perfectly_cache_keys", array_values(array_unique($pck)));

                return parent::get($columns);
            });
        }

        return parent::get($columns);
    }

    /**
     * @param bool $status
     * @return $this
     */
    public function skipCache($status = true) {
        $this->cacheSkip = $status;
        return $this;
    }

    /**
     * @param int $minutes
     * @return QueryBuilder
     */
    public function remember(int $minutes) :self {
        $this->cacheMinutes = $minutes;

        return $this;
    }
}
