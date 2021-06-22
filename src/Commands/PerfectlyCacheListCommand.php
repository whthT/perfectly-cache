<?php
/**
 * Created by PhpStorm.
 * User: Musa
 * Date: 25.03.2019
 * Time: 17:26
 */

namespace Whtht\PerfectlyCache\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class PerfectlyCacheListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'perfectly-cache:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Shows perfectly cache statistics';

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function handle()
    {
        $keys = Cache::get("perfectly_cache_keys", []);

        $this->info("[PerfectlyCache] Total key(s) count: " . count($keys));

        return 0;
    }
}
