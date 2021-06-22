<?php

namespace Whtht\PerfectlyCache\Tests\Unit\Commands;

use Whtht\PerfectlyCache\PerfectlyCache;
use Whtht\PerfectlyCache\Tests\Models\Post;
use Whtht\PerfectlyCache\Tests\Models\PostWithCache;
use Whtht\PerfectlyCache\Tests\Models\User;
use Whtht\PerfectlyCache\Tests\Models\UserWithCache;
use Whtht\PerfectlyCache\Tests\TestCase;
use Illuminate\Support\Facades\Cache;

class ClearCacheCommandTest extends TestCase
{

    public function test_clear_cache_by_table_singular_works() {
        UserWithCache::with('cached_posts')->first();
        $this->assertCount(2, Cache::get("perfectly_cache_keys", []));

        $this->artisan("perfectly-cache:clear posts");

        $this->assertCount(1, Cache::get("perfectly_cache_keys", []));
    }

    public function test_clear_cache_by_table_plural_works() {
        UserWithCache::with('cached_posts')->first();
        $this->assertCount(2, Cache::get("perfectly_cache_keys", []));

        $this->artisan("perfectly-cache:clear posts users");

        $this->assertCount(0, Cache::get("perfectly_cache_keys", []));
    }

    public function test_clear_cache_without_table_name() {
        UserWithCache::with('cached_posts')->first();
        $this->assertCount(2, Cache::get("perfectly_cache_keys", []));

        $this->artisan("perfectly-cache:clear");

        $this->assertCount(0, Cache::get("perfectly_cache_keys", []));
    }
}
