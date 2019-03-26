<?php

namespace Whtht\PerfectlyCache\Tests\Mock;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $connection = "sqlite";
    protected $table = "users";
}
