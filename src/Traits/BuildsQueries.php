<?php
/**
 * Created by PhpStorm.
 * User: Musa
 * Date: 31.01.2019
 * Time: 19:07
 */

namespace Whtht\PerfectlyCache\Traits;


use Whtht\PerfectlyCache\Facade\PerfectlyCache;

trait BuildsQueries
{
    public function first($columns = ['*'])
    {
        return PerfectlyCache::first($columns, $this);
    }

}