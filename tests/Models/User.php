<?php

namespace Whtht\PerfectlyCache\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = "users";
    public $timestamps = false;
    protected $fillable = ["name"];

    public function posts() {
        return $this->hasMany(Post::class);
    }
}
