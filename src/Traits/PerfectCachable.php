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

trait PerfectCachable
{
    public $isPerfectCachable = true;
    protected $jsonPath = 'framework/cache/perfectly-cache.json';
    protected function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();
        return new QueryBuilder(
            $connection, $connection->getQueryGrammar(), $connection->getPostProcessor()
        );
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
        $path = storage_path($this->jsonPath);
        if(file_exists($path)) {
            $file = json_decode(file_get_contents($path), true);

            return !is_array($file) || is_null($file) ? [] : $file;
        }
        return [];
    }

    public function saveJson(array $content) {
        $path = storage_path($this->jsonPath);
        file_put_contents($path, json_encode($content));
    }

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
            $this->saveJson($json);
        }

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