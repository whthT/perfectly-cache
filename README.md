# PerfectlyCache

Laravel eloquent query cache package.

It serves to cache and use any queries you make without having to make any changes to the database, system, or queries.

### Installing

- Require package

```
composer required whtht/perfectly-cache
```

- Vendor publishing 
````bash
php artisan vendor:publish --provider=Whtht\PerfectlyCache\PerfectlyCacheServiceProvider
````

- Use trait on your models 
    >Add this code in your models / or add just your base model

```
    use \Whtht\PerfectlyCache\Traits\PerfectlyCache;
```
like this
````php
<?php

namespace App;

use Whtht\PerfectlyCache\Traits\PerfectCachable;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use PerfectCachable;
}

````

All database queries you make through this model will be cached and will be read from the cache instead of the database when needed.

## Configuration
````php
// config('perfectly-cache.(name)')
// Eq: config('perfecyly-cache.enabled')
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

    "allowed" => [
        "get" => true, // Allow with 'get' function. (Eq: Model::get())
        "first" => true // Allow with 'first' function. (Eq: Model::first(); Model::find(); Model::findOrFail() )
    ]

];
````

## Cache Skipping
- With Chain 
```php
    // ->skipCache();
    $result = Category::select("id", "name")->skipCache()->get();
```
- With Eager Load
```php
    $results = Category::select("id", "name")->with([
        "_list_category_tags:id,category_id,name,slug"
    ])->find($id);
    /**
     * 2 cache will be generated in this query.
     * For the first cache categories table,
     * the second cache will occur for the list of categories of labels.
    */ 
    
    /**
    * Thanks to the ^ sign, you can prevent your relationships from being cached.
    */
    $results = Category::select("id", "name")->with([
        "^_list_category_tags:id,category_id,name,slug"
    ])->find($id);
    /**
    * It will no longer be hidden in the cache for the tag table of the categories.
    */
   
```

## Example

````php
// Basic cache
$results = \App\Category::find($id);

// Basic cache skip
$results = \App\Category::skipCache()->find($id);

````

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details