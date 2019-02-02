<?php

namespace Whtht\PerfectlyCache;

use Whtht\PerfectlyCache\Listeners\CacheMissed;
use Illuminate\Cache\Events\CacheMissed as CacheMissedEvent;
use Illuminate\Support\ServiceProvider;

class PerfectlyCacheServiceProvider extends ServiceProvider {
    public $defer = false;

    protected $listen = [
        CacheMissedEvent::class => [
            CacheMissed::class
        ],
    ];

    public function boot() {
        
        $this->publishes([
            __DIR__."/config.php" => config_path("perfectly-cache.php")
        ]);

        $events = app('events');
        foreach ($this->listen as $event => $listeners) {
            foreach ($listeners as $listener) {
                $events->listen($event, $listener);
            }
        }
    }

    public function register() {
        $this->mergeConfigFrom(__DIR__."/config.php", "perfectly-cache.php");
    }

    public function listens()
    {
        dd($this->listen);
        return $this->listen;
    }
}