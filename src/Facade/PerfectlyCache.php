<?php
/**
 * Created by PhpStorm.
 * User: Musa
 * Date: 28.01.2019
 * Time: 18:17
 */

namespace Whtht\PerfectlyCache\Facade;

use Illuminate\Support\Facades\Facade;

class PerfectlyCache extends Facade
{
    public static function getFacadeAccessor() {
        return 'perfectly-cache';
    }

    public static function a() {
        return "sd";
    }

}