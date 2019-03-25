<?php
/**
 * Created by PhpStorm.
 * User: Musa
 * Date: 23.03.2019
 * Time: 15:24
 */

namespace Whtht\PerfectlyCache\Providers;

use Whtht\PerfectlyCache\Extensions\PerfectlyStore;
use Whtht\PerfectlyCache\PerfectlyCache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class PerfectlyCacheServiceProvider extends ServiceProvider
{
    protected $defer = false, $cacheStore;

    public function __construct(\Illuminate\Contracts\Foundation\Application $app)
    {
        $this->cacheStore = config('perfectly-cache.cache-store', 'perfectly-cache');
        parent::__construct($app);
    }

    public function boot() {
        $this->registerSingletons();
        $this->registerAlias();
        $this->publish();
        $this->registerCacheStore();
    }

    public function register()
    {
        $this->registerConfigs();
    }

    /**
     * Register singletons to app
     */
    protected function registerSingletons() {
        $this->app->singleton(PerfectlyCache::class);
    }

    /**
     * Register alias to app
     */
    protected function registerAlias() {
        $this->app->alias(PerfectlyCache::class, "perfectly-cache");
    }

    /**
     * Publish vendors
     */
    protected function publish() {
        $this->publishes([
            __DIR__. '/../config/config.php' => config_path('perfectly-cache.php')
        ]);
    }

    /**
     * Register Perfectly Cache Configs
     */
    protected function registerConfigs() {
        /**
         * Register cachind array for minimize duplicate cache hits
         */

        config()->set('perfectly-cache.caching', []);

        config()->set('cache.stores.'.$this->cacheStore, [
            'driver' => $this->cacheStore
        ]);

        config()->set('filesystems.disks.'.$this->cacheStore, [
            'driver' => 'local',
            'root' => storage_path('framework/cache/'.$this->cacheStore),
        ]);

    }

    protected function registerCacheStore() {
        Cache::extend($this->cacheStore, function() {
            return Cache::repository(new PerfectlyStore);
        });
    }
}
