<?php
/**
 * Created by PhpStorm.
 * User: Musa
 * Date: 2.02.2019
 * Time: 11:10
 */

namespace Whtht\PerfectlyCache\Listeners;


use Illuminate\Cache\Events\CacheMissed;

class CacheKeyForgotten
{
    public function handle(CacheMissed $event)
    {
        dd($event, "CacheKeyForgotten");
    }
}