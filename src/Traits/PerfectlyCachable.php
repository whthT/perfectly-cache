<?php

namespace Whtht\PerfectlyCache\Traits;


use Whtht\PerfectlyCache\Builders\EloquentBuilder;
use Whtht\PerfectlyCache\Builders\QueryBuilder;
use Whtht\PerfectlyCache\Events\ModelEvents;
use Whtht\PerfectlyCache\PerfectlyCache;

trait PerfectlyCachable
{
    use CacheGettersSetters;

    protected function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();
        $queryBuilder =  new QueryBuilder(
            $connection, $connection->getQueryGrammar(), $connection->getPostProcessor()
        );

        $queryBuilder->isCacheEnable = $this->isCacheEnable;

        $queryBuilder->cacheMinutes = $this->cacheMinutes ?: config('perfectly-cache.minutes', PerfectlyCache::$defaultCacheMinutes);

        return $queryBuilder;
    }

    public function newEloquentBuilder($query)
    {
        return new EloquentBuilder($query);
    }

    public function setModel(PerfectlyCache $model)
    {
        $this->model = $model;

        $this->query->from($model->getTable());

        return $this;
    }

    public function newModelQuery()
    {
        return $this->newEloquentBuilder(
            $this->newBaseQueryBuilder()
        )->setModel($this);
    }

    public function controlForCache(string $event) {
        $supportedEvents = config('perfectly-cache.clear_events', ['created', 'updated', 'deleted']);

        if (in_array($event, $supportedEvents)) {
            PerfectlyCache::clearCacheByTable($this->getTable());
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

    public function reloadCache() {
        PerfectlyCache::clearCacheByTable($this->getTable());
    }
}
