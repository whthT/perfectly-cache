<?php
/**
 * Created by PhpStorm.
 * User: Musa
 * Date: 23.03.2019
 * Time: 15:24
 */

namespace Whtht\PerfectlyCache\Providers;

use Whtht\PerfectlyCache\Commands\PerfectlyCacheClearCommand;
use Whtht\PerfectlyCache\Commands\PerfectlyCacheListCommand;
use Whtht\PerfectlyCache\Events\ModelEvents;
use Whtht\PerfectlyCache\Extensions\PerfectlyStore;
use Whtht\PerfectlyCache\Listeners\ModelDispactEventListener;
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
        $this->registerCommands();
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

    protected function isPerfectlyStoreSelected() {
        return config('perfectly-cache.store') === 'perfectly-cache';
    }
    /**
     * Register Perfectly Cache Configs
     */
    protected function registerConfigs() {
        /**
         * Register cachind array for minimize duplicate cache hits
         */
        if ($this->isPerfectlyStoreSelected() || $this->isTesting()) {
            config()->set('perfectly-cache.caching', []);

            config()->set('cache.stores.'.$this->cacheStore, [
                'driver' => $this->cacheStore
            ]);
        }

        $cacheDiskPath = storage_path('framework/cache/'.$this->cacheStore);

        if ($this->isTesting()) {
            $cacheDiskPath = __DIR__.'/../../tests/cache-storage';
        }

        config()->set('filesystems.disks.perfectly-cache', [
            'driver' => 'local',
            'root' => $cacheDiskPath,
        ]);
    }

    protected function isTesting() {
        return config('app.env') == "testing";
    }

    protected function registerCacheStore() {
        if ($this->isPerfectlyStoreSelected() || $this->isTesting()) {
            Cache::extend($this->cacheStore, function() {
                return Cache::repository(new PerfectlyStore);
            });
        }
    }

    protected function registerCommands() {
        if ($this->app->runningInConsole()) {
            $this->commands([
                PerfectlyCacheClearCommand::class,
                PerfectlyCacheListCommand::class
            ]);
        }
    }
}
