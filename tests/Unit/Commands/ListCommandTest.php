<?php

namespace Whtht\PerfectlyCache\Tests\Unit\Commands;

use Whtht\PerfectlyCache\PerfectlyCache;
use Whtht\PerfectlyCache\Tests\Models\Post;
use Whtht\PerfectlyCache\Tests\Models\PostWithCache;
use Whtht\PerfectlyCache\Tests\Models\User;
use Whtht\PerfectlyCache\Tests\Models\UserWithCache;
use Whtht\PerfectlyCache\Tests\TestCase;
use Illuminate\Support\Facades\Cache;

class ListCommandTest extends TestCase
{

    public function test_list_command_works() {

        $response = $this->artisan("perfectly-cache:list")->run();
        $this->assertEquals(0, $response);

        UserWithCache::with('cached_posts')->first();

        $response = $this->artisan("perfectly-cache:list")->run();
        $this->assertEquals(0, $response);
    }
}
