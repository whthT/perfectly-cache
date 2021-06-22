<?php

namespace Whtht\PerfectlyCache\Tests\Models;


use Whtht\PerfectlyCache\Traits\PerfectlyCachable;
use Illuminate\Database\Eloquent\Model;

class UserWithCache extends Model
{
    use PerfectlyCachable;
    public $timestamps = false;
    protected $table = "users";

    protected $fillable = ["name"];

    public function posts() {
        return $this->hasMany(Post::class, 'user_id', 'id');
    }

    public function cached_posts() {
        return $this->hasMany(PostWithCache::class, 'user_id', 'id');
    }
}
