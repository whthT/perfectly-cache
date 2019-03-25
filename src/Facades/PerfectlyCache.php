<?php
/**
 * Created by PhpStorm.
 * User: Musa
 * Date: 23.03.2019
 * Time: 15:36
 */

namespace Whtht\PerfectlyCache\Facades;


use Illuminate\Support\Facades\Facade;

/**
 * Class PerfectlyCache
 * @package Whtht\PerfectlyCache\Facades
 * @see \Whtht\PerfectlyCache\PerfectlyCache
 */
class PerfectlyCache extends Facade
{
    protected static function getFacadeAccessor() {
        return 'perfectly-cache';
    }
}
