<?php
/**
 * Created by PhpStorm.
 * User: Musa
 * Date: 26.03.2019
 * Time: 11:29
 */

namespace Whtht\PerfectlyCache\Tests;


use Whtht\PerfectlyCache\Providers\PerfectlyCacheServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function __construct($name = null, array $data = [], string $dataName = null)
    {
        parent::__construct($name, $data, $dataName);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->clearCacheList();

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        $this->artisan('migrate', ['--database' => 'testing']);
    }

    public function clearCacheList() {
        $this->artisan('perfectly-cache:clear');
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');

        $app['config']->set('database.connections.testing.prefix', 'perfectly_cache_test_');
    }

    protected function getPackageProviders($app)
    {
        return [
            PerfectlyCacheServiceProvider::class
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'PerfectlyCache' => 'Whtht\PerfectlyCache\Facades\PerfectlyCache'
        ];
    }

}
