<?php
/**
 * Created by PhpStorm.
 * User: Musa
 * Date: 31.01.2019
 * Time: 16:52
 */

namespace Whtht\PerfectlyCache\Builders;


use Whtht\PerfectlyCache\Facade\PerfectlyCache;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;

class QueryBuilder extends Builder
{
    public $cacheSkip;
    public $cacheRememberMinutes = 0;

    public function getCacheSkip() {
        return $this->cacheSkip;
    }

    public function remember(int $minutes = 30) {
        if($minutes <= 0) {
            $minutes = 30;
        }

        $this->cacheRememberMinutes = $minutes;
    }

    public function runSelect()
    {
        return $this->connection->select(
            $this->toSql(), $this->getBindings(), ! $this->useWritePdo
        );
    }

    public function onceWithColumns($columns, $callback)
    {
        $original = $this->columns;

        if (is_null($original)) {
            $this->columns = $columns;
        }

        $result = $callback();

        $this->columns = $original;

        return $result;
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
     * @param array $columns
     * @return \Illuminate\Support\Collection
     */
    public function get($columns = ['*'])
    {
        $result = PerfectlyCache::get($columns, $this, $this->cacheSkip);
        PerfectlyCache::saveToJson();
        return $result;
    }
}