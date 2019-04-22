<?php
/**
 * Created by PhpStorm.
 * User: Musa
 * Date: 28.01.2019
 * Time: 18:19
 */

return [

    "enabled" => true, // Is cache enabled?

    "minutes" => 30, // Cache minutes.

    /**
     * If this event is triggered on this model,
     * the cache of that table is deleted.
     */
    "clear_events" => [
        "created",
        "updated",
        "deleted"
    ],

    'store' => 'perfectly-cache', // perfectly-cache is default cache store, Usable: database, file, array, etc.

];
