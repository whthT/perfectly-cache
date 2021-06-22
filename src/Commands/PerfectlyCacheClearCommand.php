<?php
/**
 * Created by PhpStorm.
 * User: Musa
 * Date: 25.03.2019
 * Time: 16:59
 */

namespace Whtht\PerfectlyCache\Commands;


use Illuminate\Console\Command;
use Whtht\PerfectlyCache\PerfectlyCache;

class PerfectlyCacheClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'perfectly-cache:clear {table?*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all cache from PerfectlyCache';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function handle()
    {
        if ($table = $this->argument('table')) {
            $this->info("[PerfectlyCache] Clear progress: ".implode(', ', $table));

            $total = PerfectlyCache::clearCacheByTable($table);

        } else {
            $this->info("[PerfectlyCache] Clearing all caches..");
            $total = PerfectlyCache::clearAllCaches();
        }

        $this->info("[PerfectlyCache] Cache clearing completed. [OK]");
        $this->info("[PerfectlyCache] Successfully cleared $total cache in total");
    }
}
