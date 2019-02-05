<?php
/**
 * Created by PhpStorm.
 * User: Musa
 * Date: 28.01.2019
 * Time: 19:09
 */

namespace Whtht\PerfectlyCache\Traits;

use Whtht\PerfectlyCache\Builders\QueryBuilder;
use Whtht\PerfectlyCache\Builders\EloquentBuilder;

use Whtht\PerfectlyCache\Facade\PerfectlyCache;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait PerfectCachable
{
    public $isPerfectCachable = true;

    protected function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();
        $queryBuilder =  new QueryBuilder(
            $connection, $connection->getQueryGrammar(), $connection->getPostProcessor()
        );

        $queryBuilder->isPerfectCachable = $this->getIsPerfectCachable();

        if (isset($this->cacheMinutes) && $this->cacheMinutes > 0) {
            $queryBuilder->cacheRememberMinutes = $this->cacheMinutes;
        }

        return $queryBuilder;
    }

    protected function getIsPerfectCachable() {
        return $this->isPerfectCachable;
    }

    public function newEloquentBuilder($query)
    {
        return new EloquentBuilder($query);
    }

    public function newModelQuery()
    {
        return $this->newEloquentBuilder(
            $this->newBaseQueryBuilder()
        )->setModel($this);
    }

    public function setModel(PerfectlyCache $model)
    {
        $this->model = $model;

        $this->query->from($model->getTable());

        return $this;
    }

    public function getCacheJson() {
        $path = storage_path("framework/cache/perfectly-cache.json");
        if(file_exists($path)) {
            $file = json_decode(file_get_contents($path), true);

            return !is_array($file) || is_null($file) ? [] : $file;
        }
        return [];
    }

    /**
     * @return $this
     */
    public function reloadCache()
    {
        $json = $this->getCacheJson();
        $table = $this->getTable();
        $has_action = false;
        if (array_key_exists($table, $json)) {
            foreach ($json[$table] as $key => $cacheKey) {
                if (Cache::has($cacheKey)) {
                    Cache::forget($cacheKey);
                    unset($json[$table][$key]);

                    if (!$has_action) {
                        $has_action = true;
                    }
                }
            }
        }

        if ($has_action) {
            PerfectlyCache::saveToJson($json);
        }

        return $this;

    }

    public function controlForCache(string $event) {
        $supportedEvents = config('perfectly-cache.clear_events', ['created', 'updated', 'deleted']);
        if (in_array($event, $supportedEvents)) {
            self::reloadCache();
        }
    }

    protected function fireCustomModelEvent($event, $method)
    {
        $this->controlForCache($event);

        if (! isset($this->dispatchesEvents[$event])) {
            return;
        }

        $result = static::$dispatcher->$method(new $this->dispatchesEvents[$event]($this));

        if (! is_null($result)) {
            return $result;
        }
    }
}