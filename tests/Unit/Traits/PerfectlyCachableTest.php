<?php

namespace Whtht\PerfectlyCache\Tests\Unit\Traits;

use Whtht\PerfectlyCache\PerfectlyCache;
use Whtht\PerfectlyCache\Tests\Models\Post;
use Whtht\PerfectlyCache\Tests\Models\PostWithCache;
use Whtht\PerfectlyCache\Tests\Models\User;
use Whtht\PerfectlyCache\Tests\Models\UserWithCache;
use Whtht\PerfectlyCache\Tests\TestCase;
use Illuminate\Support\Facades\Cache;

class PerfectlyCachableTest extends TestCase
{

    public function test_reload_cache_method_works() {
        $user = UserWithCache::with('posts')->first();
        $this->assertCount(1, Cache::get("perfectly_cache_keys", []));

        $user->reloadCache();

        $this->assertCount(0, Cache::get("perfectly_cache_keys", []));
    }

    public function test_get_is_cache_enable_method_works() {
        $user = UserWithCache::with('posts')->first();
        $this->assertTrue($user->getIsCacheEnabled());
    }

    public function test_set_is_cache_enable_method_works() {
        $user = UserWithCache::with('posts')->first();

        $this->assertTrue($user->getIsCacheEnabled());

        $user->setIsCacheEnabled(false);

        $this->assertFalse($user->getIsCacheEnabled());
    }
}
