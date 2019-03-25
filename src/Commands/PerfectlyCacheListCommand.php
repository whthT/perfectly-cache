<?php
/**
 * Created by PhpStorm.
 * User: Musa
 * Date: 25.03.2019
 * Time: 17:26
 */

namespace Whtht\PerfectlyCache\Commands;


use Carbon\Carbon;
use Whtht\PerfectlyCache\PerfectlyCache;
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
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function handle()
    {
        $store = config('perfectly-cache.cache-store', 'perfectly-cache');

        $filesystem = Cache::store($store)->getFilesystem();

        $files = $filesystem->allFiles(Cache::store($store)->getDirectory()->path(''));

        $list = [];

        foreach ($files as $file) {

            $name = $file->getFilename();

            $split = explode('_', $name);

            if (!array_key_exists($split[0], $list)) {
                $list[$split[0]] = [];
            }

            $deadTime = explode('.', $split[1])[1];

            $createdDate = Carbon::parse($file->getCTime());

            $list[] = [
                $split[0],
                $createdDate->toDateTimeString(),
                $createdDate->diffForHumans(),
                $createdDate->addMinutes($deadTime)->toDateTimeString(),
                $createdDate->diffForHumans(),
                $this->formatBytes($file->getSize())
            ];
        }
        $this->table([
            'Table', 'Created At', 'Created At For Humans', 'Dead At', 'Dead At For Humans', 'Size'
        ], $list);

    }
}
