<?php
/**
 * Created by PhpStorm.
 * User: Musa
 * Date: 26.03.2019
 * Time: 15:01
 */

namespace Whtht\PerfectlyCache\Tests\Models;


use Whtht\PerfectlyCache\Traits\PerfectlyCachable;
use Illuminate\Database\Eloquent\Model;

class PostWithCache extends Model
{
    use PerfectlyCachable;

    protected $table = "posts";
    public $timestamps = false;
}
