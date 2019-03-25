<?php
/**
 * Created by PhpStorm.
 * User: Musa
 * Date: 25.03.2019
 * Time: 16:59
 */

namespace Whtht\PerfectlyCache\Commands;


use Whtht\PerfectlyCache\Facades\PerfectlyCache;
use Illuminate\Console\Command;

class PerfectlyCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'perfectly-cache:clear';

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
     */
    public function handle()
    {
        PerfectlyCache::clearAllCaches();
    }
}
