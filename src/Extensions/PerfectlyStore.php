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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\InteractsWithTime;

class PerfectlyStore implements PerfectlyStoreInterface
{
    use InteractsWithTime;
    protected $store, $filesystem;

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

    /**
     * @return array
     */
    protected function emptyPayload()
    {
        return ['data' => null, 'time' => null];
    }

    /**
     * @return int
     */
    protected function currentTime()
    {
        return now()->getTimestamp();
    }

    /**
     * @param array|string $key
     * @return \Illuminate\Config\Repository|mixed|null
     */
    public function get($key)
    {
        if ($this->existsInConfig($key)) {
            return $this->getFromConfig($key);
        }

        return $this->getPayload($key)['data'] ?? null;
    }

    /**
     * @param string $key
     * @return array
     */
    protected function getPayload(string $key) {
        try {

            $expire = substr($result = $this->filesystem->get($this->getCacheFile($key)), 0, 10);

        }catch (\Exception $exception) {
            return $this->emptyPayload();
        }

        if ($this->currentTime() >= $expire) {
            $this->forget($key);

            return $this->emptyPayload();
        }


        $result = PerfectlyCache::uncompressOutput(substr($result, 10, strlen($result)));

        config()->set('perfectly-cache.caching.'.$key, $result);

        return [
            'data' => $result,
            'time' => $expire - $this->currentTime()
        ];
    }

    /**
     * @param string $key
     * @return bool
     */
    public function existsInConfig(string $key) {
        return config('perfectly-cache.caching.'.$key) ? true : false;
    }

    /**
     * @param string $key
     * @return \Illuminate\Config\Repository|mixed
     */
    public function getFromConfig(string $key) {
        return config('perfectly-cache.caching.'.$key);
    }

    /**
     * @param boolean
     */
    public function exists(string $key) {

        if ($this->existsInConfig($key)) {
            return true;
        }

        return $this->filesystem->exists($this->getCacheFile($key));
    }

    /**
     * @param string $key
     * @return string
     */
    public function getCacheFile(string $key)
    {
        return $this->getDirectory()->path($key);
    }

    /**
     * @return \Illuminate\Contracts\Filesystem\Filesystem|string
     */
    public function getDirectory() {
        return Storage::disk($this->store);
    }


    /**
     * @param string $key
     * @param mixed $value
     * @param int $seconds
     * @return bool
     */
    public function put($key, $value, $seconds)
    {
        config()->set('perfectly-cache.caching.'.$key, $value);

        $value = $this->expiration($seconds).PerfectlyCache::compressOutput($value);

        $result = $this->filesystem->put($this->getCacheFile($key), $value, true);

        return $result !== false && $result > 0;
    }

    /**
     * @param $seconds
     * @return int
     */
    protected function expiration($seconds)
    {
        $time = $this->availableAt($seconds);

        return $seconds === 0 || $time > 9999999999 ? 9999999999 : $time;
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

    /**
     * @param string $table
     * @return bool
     */
    public function forget($key)
    {
        return $this->filesystem->delete($this->getDirectory()->path($key));
    }

    /**
     * @param mixed ...$table
     * @return bool
     */
    public function forgetByTable(...$table) {
        $table = collect($table)->flatten();
        $pass = 0;
        foreach ($table as $item) {
            $keys = $this->filesystem->glob($this->getDirectory()->path($item).'_-_*');
            if ($this->filesystem->delete($keys)) {
                $pass += count($keys);
            }
        }

        return $pass;
    }

    /**
     * @return Filesystem
     */
    public function getFilesystem() {
        return $this->filesystem;
    }


    /**
     * @return bool
     */
    public function flush()
    {
        $pass = 0;

        foreach ($this->filesystem->allFiles($this->getDirectory()->path('')) as $file) {
            if ($this->filesystem->delete($file)) {
                $pass++;
            }
        }

        return $pass;
    }

    public function getPrefix()
    {
        // TODO: Implement getPrefix() method.
    }
}
