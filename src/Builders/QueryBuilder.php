<?php
/**
 * Created by PhpStorm.
 * User: Musa
 * Date: 23.03.2019
 * Time: 16:12
 */

namespace Whtht\PerfectlyCache\Builders;


use Whtht\PerfectlyCache\PerfectlyCache;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Cache;

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
     * The progress for cache remember.
     * Last action of PerfectlyCache
     * @param array $columns
     * @return \Illuminate\Config\Repository|\Illuminate\Support\Collection|mixed
     */
    public function rememberProgress($columns = ["*"]) {

        $cacheEnabled = config('perfectly-cache.enabled', true);
        $cacheStore = config('perfectly-cache.cache-store', 'perfectly-cache');

        if ($cacheEnabled && $this->isCacheEnable && ! $this->cacheSkip) {

            $this->cacheKey = PerfectlyCache::generateCacheKey($this);

            $calculatedCacheMinutes = PerfectlyCache::calcultateCacheMinutes($this->cacheMinutes);

            return Cache::store($cacheStore)->remember(
                $this->cacheKey, $calculatedCacheMinutes, function () use($columns) {

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
