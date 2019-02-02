<?php
/**
 * Created by PhpStorm.
 * User: Musa
 * Date: 2.02.2019
 * Time: 11:10
 */

namespace Whtht\PerfectlyCache\Listeners;


use Whtht\PerfectlyCache\Facade\PerfectlyCache;
use Illuminate\Cache\Events\CacheMissed as CacheMissedEvent;

class CacheMissed
{
    public function handle(CacheMissedEvent $event)
    {
        $json = collect(PerfectlyCache::getCacheJsonFile());
        $mustSave = false;
        if(in_array($event->key, $json->flatten()->toArray())) {
            foreach ($json as $table => $cacheKeys) {
                $position = array_search($event->key, $cacheKeys);
                if($position >= 0) {
                    unset($cacheKeys[$position]);
                    $json[$table] = $cacheKeys;
                    $mustSave = true;
                    break;
                }
            }
        }

        if ($mustSave) {
            PerfectlyCache::saveToJson($json->toArray());
        }
    }
}