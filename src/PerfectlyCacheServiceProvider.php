<?php

namespace Whtht\PerfectlyCache;

use Illuminate\Support\ServiceProvider;

class PerfectlyCacheServiceProvider extends ServiceProvider {
    public $defer = false;

    public function boot() {
        
        $this->publishes([
            __DIR__."/config.php" => config_path("perfectly-cache.php")
        ]);
    }

    public function register() {
        $this->mergeConfigFrom(__DIR__."/config.php", "perfectly-cache.php");
    }
}