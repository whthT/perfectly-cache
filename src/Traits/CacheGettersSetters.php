<?php
/**
 * Created by PhpStorm.
 * User: Musa
 * Date: 26.03.2019
 * Time: 15:35
 */

namespace Whtht\PerfectlyCache\Traits;


trait CacheGettersSetters
{
    protected $isCacheEnable = true;
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
