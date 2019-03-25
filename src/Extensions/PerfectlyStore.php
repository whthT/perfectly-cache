<?php
/**
 * Created by PhpStorm.
 * User: Musa
 * Date: 25.03.2019
 * Time: 12:41
 */

namespace Whtht\PerfectlyCache\Extensions;


use Whtht\PerfectlyCache\Contracts\PerfectlyStoreInterface;
use Whtht\PerfectlyCache\PerfectlyCache;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class PerfectlyStore implements PerfectlyStoreInterface
{
    protected $store, $filesystem, $cacheFileExt = 'pc';
    public function __construct()
    {
        $this->filesystem = new Filesystem();
        $this->store = config('perfectly-cache.cache-directory', 'perfectly-cache');
    }

    /**
     * @return \Illuminate\Config\Repository|mixed
     */
    public function getStore() {
        return $this->store;
    }

    public function many(array $keys)
    {
        // TODO: Implement many() method.
    }

    public function get($key)
    {
        if ($this->exists($key)) {
            return PerfectlyCache::uncompressOutput($this->filesystem->get($this->getCacheFile($key)));
        }

        return null;
    }

    /**
     * @param boolean
     */
    public function exists(string $key) {
        return $this->filesystem->exists($this->getCacheFile($key));
    }

    public function getCacheFile(string $key)
    {
        return $this->getDirectory()->path($this->combineCacheName($key));
    }

    public function getDirectory() {
        return Storage::disk($this->store);
    }

    public function combineCacheName(string $key) {
        return $key.".".$this->getCacheFileExt();
    }

    /**
     * @return string
     */
    public function getCacheFileExt() {
        return $this->cacheFileExt;
    }


    public function put($key, $value, $seconds)
    {

        $value = PerfectlyCache::compressOutput($value);

        $result = $this->filesystem->put($this->getCacheFile($key), $value, false);

        return $result !== false && $result > 0;
    }

    public function putMany(array $values, $seconds)
    {
        // TODO: Implement putMany() method.
    }

    public function increment($key, $value = 1)
    {
        // TODO: Implement increment() method.
    }

    public function decrement($key, $value = 1)
    {
        // TODO: Implement decrement() method.
    }

    public function forever($key, $value)
    {
        // TODO: Implement forever() method.
    }

    public function forget($table)
    {
        $files = $this->filesystem->glob($this->getDirectory()->path('').$table."_*.".$this->getCacheFileExt());

        $this->filesystem->delete($files);

        return true;
    }

    public function flush()
    {
        // TODO: Implement flush() method.
    }

    public function getPrefix()
    {
        // TODO: Implement getPrefix() method.
    }
}
