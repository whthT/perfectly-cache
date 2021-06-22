<?php

namespace Whtht\PerfectlyCache\Tests\Unit;

use Whtht\PerfectlyCache\PerfectlyCache;
use Whtht\PerfectlyCache\Tests\Models\Post;
use Whtht\PerfectlyCache\Tests\Models\PostWithCache;
use Whtht\PerfectlyCache\Tests\Models\User;
use Whtht\PerfectlyCache\Tests\Models\UserWithCache;
use Whtht\PerfectlyCache\Tests\TestCase;
use Illuminate\Support\Facades\Cache;

class CachingTest extends TestCase
{

    public function test_cache_with_pc_works() {
        UserWithCache::with('posts')->first();
        $this->assertCount(1, Cache::get("perfectly_cache_keys", []));
    }

    public function test_cache_without_pc_works() {
        User::with('posts')->first();
        $this->assertCount(0, Cache::get("perfectly_cache_keys", []));
    }

    public function test_cache_works() {
        UserWithCache::first();
        $this->assertCount(1, Cache::get("perfectly_cache_keys", []));
    }

    public function test_skip_cache_in_query_works() {
        UserWithCache::skipCache()->first();
        $this->assertCount(0, Cache::get("perfectly_cache_keys", []));
    }

    public function test_cache_works_with_bindings() {
        User::with('posts:id,name,user_id')->where('id', '>=', 0)->first();
        $this->assertCount(0, Cache::get("perfectly_cache_keys", []));

        UserWithCache::with('posts:id,name,user_id')->where('id', '>=', 0)->first();
        $this->assertCount(1, Cache::get("perfectly_cache_keys", []));

        UserWithCache::with('cached_posts:id,name,user_id')->where('id', '>=', 0)->first();
        $this->assertCount(2, Cache::get("perfectly_cache_keys", []));
    }

    public function test_skip_cache_in_eagerload_works() {
        UserWithCache::with('cached_posts')->first();
        $this->assertCount(2, Cache::get("perfectly_cache_keys", []));

        PerfectlyCache::clearAllCaches();
        $this->assertCount(0, Cache::get("perfectly_cache_keys", []));

        UserWithCache::with('^cached_posts')->first();
        $this->assertCount(1, Cache::get("perfectly_cache_keys", []));
    }

    public function test_cache_minutes_works_in_eagerload() {
        UserWithCache::with('(5)cached_posts')->first();
        $this->assertCount(2, Cache::get("perfectly_cache_keys", []));

        $usersKey = Cache::get("perfectly_cache_keys", [])[0];
        $postsKey = Cache::get("perfectly_cache_keys", [])[1];

        $this->assertNotNull(Cache::get($usersKey));
        $this->assertNotNull(Cache::get($postsKey));

        $this->travel(6)->minutes();

        $this->assertNotNull(Cache::get($usersKey));
        $this->assertNull(Cache::get($postsKey));

    }

    public function test_cache_minutes_works() {
        UserWithCache::first();
        $this->assertCount(1, Cache::get("perfectly_cache_keys", []));
        $usersKey = Cache::get("perfectly_cache_keys", [])[0];

        $this->assertTrue(Cache::has($usersKey));

        $this->travel(29)->minutes();

        $this->assertTrue(Cache::has($usersKey));

        $this->travelBack();
        $this->travel(31)->minutes();

        $this->assertFalse(Cache::has($usersKey));

    }

    public function test_remember_method_works() {
        UserWithCache::remember(10)->first();
        $this->assertCount(1, Cache::get("perfectly_cache_keys", []));
        $usersKey = Cache::get("perfectly_cache_keys", [])[0];

        $this->assertTrue(Cache::has($usersKey));

        $this->travel(9)->minutes();

        $this->assertTrue(Cache::has($usersKey));

        $this->travelBack();
        $this->travel(11)->minutes();

        $this->assertFalse(Cache::has($usersKey));
    }

    public function test_forget_cache_on_record_created() {
        $user = UserWithCache::first();
        UserWithCache::select('id')->get();
        PostWithCache::first();
        $this->assertCount(3, Cache::get("perfectly_cache_keys", []));
        $usersKey = Cache::get("perfectly_cache_keys", [])[0];
        $postsKey = Cache::get("perfectly_cache_keys", [])[2];

        $this->assertTrue(Cache::has($usersKey));
        $this->assertTrue(Cache::has($postsKey));

        UserWithCache::create([
            "name" => "Test"
        ]);

        $this->assertCount(1, Cache::get("perfectly_cache_keys", []));

        $this->assertFalse(Cache::has($usersKey));
        $this->assertTrue(Cache::has($postsKey));
    }

    public function test_forget_cache_on_record_updated() {
        $user = UserWithCache::first();
        UserWithCache::select('id')->get();
        PostWithCache::first();
        $this->assertCount(3, Cache::get("perfectly_cache_keys", []));
        $usersKey = Cache::get("perfectly_cache_keys", [])[0];
        $postsKey = Cache::get("perfectly_cache_keys", [])[2];

        $this->assertTrue(Cache::has($usersKey));
        $this->assertTrue(Cache::has($postsKey));

        $user->update([
            "name" => "Test 31123"
        ]);

        $this->assertCount(1, Cache::get("perfectly_cache_keys", []));

        $this->assertFalse(Cache::has($usersKey));
        $this->assertTrue(Cache::has($postsKey));
    }

    public function test_forget_cache_on_record_deleted() {
        $user = UserWithCache::first();
        UserWithCache::select('id')->get();
        PostWithCache::first();
        $this->assertCount(3, Cache::get("perfectly_cache_keys", []));
        $usersKey = Cache::get("perfectly_cache_keys", [])[0];
        $postsKey = Cache::get("perfectly_cache_keys", [])[2];

        $this->assertTrue(Cache::has($usersKey));
        $this->assertTrue(Cache::has($postsKey));

        $user->delete();

        $this->assertCount(1, Cache::get("perfectly_cache_keys", []));

        $this->assertFalse(Cache::has($usersKey));
        $this->assertTrue(Cache::has($postsKey));
    }

}
