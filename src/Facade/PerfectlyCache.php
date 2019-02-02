<?php
/**
 * Created by PhpStorm.
 * User: Musa
 * Date: 28.01.2019
 * Time: 18:17
 */

namespace Whtht\PerfectlyCache\Facade;

use Whtht\PerfectlyCache\Builders\EloquentBuilder;
use Whtht\PerfectlyCache\Builders\QueryBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PerfectlyCache extends Facade
{
    static $outputForJson = [];
    public static function getFacadeAccessor() {
        return 'perfectly-cache';
    }

    public static function hasCache($key) {
        return Cache::has($key);
    }

    public static function isCacheAllowed($func) {
        return config()->get("perfectly-cache.allowed.".$func, false);
    }

    public static function isCacheEnabled() {
        return config()->get("perfectly-cache.enabled", false);
    }

    public static function getCacheJsonFile() {
        return @json_decode(@file_get_contents(storage_path("framework/cache/perfectly-cache.json")), true) ?? [];
    }

    public static function createCacheKey($sql) {
        return md5($sql);
    }

    protected static function getProgressor($instance, $columns) {
        return collect($instance->onceWithColumns($columns, function () use($instance) {
            return $instance->processor->processSelect($instance, $instance->runSelect());
        }));
    }

    /**
     * @param array $columns
     * @param null $instance
     * @param bool $cacheSkip
     * @return Collection|mixed
     */
    public static function get($columns = ["*"], $instance = null, $cacheSkip = false) {

        if (!is_null($instance)) {
            if($instance instanceof QueryBuilder) {

                $cleanSql = self::mergeBindings($instance->toSql(), $instance->getBindings());
                $cacheKey = self::createCacheKey($cleanSql);
                $cacheMinutes = $instance->cacheRememberMinutes > 0 ? $instance->cacheRememberMinutes : config('perfectly-cache.minutes');

                if (
                    self::isCacheEnabled() &&
                    self::isCacheAllowed("get") &&
                    !$cacheSkip &&
                    $instance->isPerfectCachable
                ) {
                    $results = Cache::remember($cacheKey, $cacheMinutes, function() use($instance, $columns) {
                        return self::getProgressor($instance, $columns);
                    });

                    self::prepareForJsonOutput($cacheKey, $instance->from);

                } else {
                    $results =  self::getProgressor($instance, $columns);
                }

                return $results;

            } else {

                return self::singleBuilder($columns, $instance);

            }


        }

        // Instance cannot be null.
    }

    public static function prepareForJsonOutput($cacheKey, $table) {
        if (!isset(self::$outputForJson[$table])) {
            self::$outputForJson[$table] = [];
        }
        self::$outputForJson[$table][] = $cacheKey;
    }

    public static function saveToJson(array $saveJson = []) {

        $json = filled($saveJson) ? $saveJson : static::$outputForJson;

        if (filled($json)) {
            $filePath = storage_path("framework/cache/perfectly-cache.json");

            if (file_exists($filePath)) {
                $jsonList = json_decode(file_get_contents($filePath), true);
                $jsonList = !is_array($jsonList) ? [] : $jsonList;
            } else {
                $jsonList = [];
            }

            foreach ($json as $key => $value) {
                if (!isset($jsonList[$key])) {
                    $jsonList[$key] = [];
                }
                $jsonList[$key] = array_values(array_unique(array_merge($jsonList[$key], $value)));
            }

            file_put_contents($filePath, json_encode($jsonList));
        }
    }

    /**
     * @param $columns
     * @param $instance
     * @return Collection
     */
    protected static function singleBuilder($columns, $instance) {
        try {
            $builder = $instance->applyScopes();

            if (count($models = $builder->getModels($columns)) > 0) {
                $models = $builder->eagerLoadRelations($models);
            }

            return $builder->getModel()->newCollection($models);
        }catch (\Exception $exception) {
            return $instance;
        }

    }

    public static function first($columns,EloquentBuilder $instance = null) {
        $result = self::singleBuilder($columns, $instance->take(1)->get());

        return $result->first();
    }

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

}