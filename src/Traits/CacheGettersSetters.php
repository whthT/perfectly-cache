<?php
/**
 * Created by PhpStorm.
 * User: Musa
 * Date: 26.03.2019
 * Time: 15:35
 */

namespace Whtht\PerfectlyCache\Traits;


use Whtht\PerfectlyCache\PerfectlyCache;

trait CacheGettersSetters
{
    protected $isCacheEnable = true;
    protected $cacheMinutes = 0;

    /**
     * @return int
     */
    public function getCacheMinutes() {
        return $this->cacheMinutes;
    }

    /**
     * @param int $minutes
     * @return $this
     */
    public function setCacheMinutes(int $minutes) {
        $this->cacheMinutes = $minutes;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsCacheEnabled() {
        return $this->isCacheEnable;
    }

    /**
     * @param bool|null $bool
     * @return $this
     */
    public function setIsCacheEnabled(?bool $bool = true) {
        $this->isCacheEnable = $bool;
        return $this;
    }

}
