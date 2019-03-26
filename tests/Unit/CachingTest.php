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
    public function testCaching() {

        $this->getUserWithNoCache();

        $this->getUserWithCache();

        $this->getUserWithNoCachedEagerLoad();

        $this->getUserWithCachedEagerLoad();

        $this->fileExistsTest();

        $this->getUserPostsWithLazyLoad();
    }

    public function getUserWithNoCache() {
        User::first();
        $this->assertIfListEmpty();
    }

    public function getUserWithCache() {
        UserWithCache::first();

        $this->assertIfSameListLength(1);
    }

    public function getUserWithNoCachedEagerLoad() {
        UserWithCache::with('posts')->first();

        $this->assertIfSameListLength(1);
    }

    public function getUserWithCachedEagerLoad() {
        UserWithCache::with('cached_posts')->first();

        $this->assertIfSameListLength(2);
    }


    /**
     * @param int $length
     */
    public function assertIfSameListLength(int $length) {
        $this->assertEquals($length, count($this->getCacheFileList()));
    }

    public function assertIfListEmpty() {
        /* Cache folder must be empty */
        $this->assertTrue(
            blank($this->getCacheFileList())
        );
    }

    /**
     * Key storage tests
     */
    public function fileExistsTest() {
        $table = "posts";
        $minutes = 15;

        $post = PostWithCache::select('id', 'name')->where('id', 20)->remember(15);


        $key = PerfectlyCache::generateCacheKey($table, $post->toSql(), $post->getBindings(), $minutes);


        $cache = Cache::store($this->cacheStore)->get($key);

        $this->assertTrue(is_null($cache));

        $post->get();

        $cache = Cache::store($this->cacheStore)->get($key);

        $this->assertTrue(!is_null($cache));

    }

    public function getUserPostsWithLazyLoad() {
        $this->assertIfSameListLength(3);

        $user = UserWithCache::first();

        $this->assertIfSameListLength(3);

        $posts = $user->cached_posts()->select('id', 'name', 'user_id')->limit(12)->get();

        $this->assertIfSameListLength(4);
    }
}
