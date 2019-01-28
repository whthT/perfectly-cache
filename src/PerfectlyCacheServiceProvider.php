<?php

namespace Whtht\PerfectlyCache;

use Illuminate\Support\ServiceProvider;

class PerfectlyCacheServiceProvider extends ServiceProvider {
    public $defer = false;

    public function boot() {
        
    }

    public function register() {
        $this->mergeConfigFrom(__DIR__."/config.php", "perfectly-cache");
    }
}