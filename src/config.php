<?php
/**
 * Created by PhpStorm.
 * User: Musa
 * Date: 28.01.2019
 * Time: 18:19
 */

return [
    
    "enabled" => true,

    "minutes" => 30,

    "events" => [
        "created",
        "updated",
        "deleted"
    ],

    "allowed" => [
        "get" => true,
        "first" => true
    ]

];