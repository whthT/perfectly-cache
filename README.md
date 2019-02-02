# PerfectlyCache

Laravel eloquent query cache package.

It serves to cache and use any queries you make without having to make any changes to the database, system, or queries.

PerfectlyCache automatically redirects the same query to the model when you need the same query by caching the results of the queries you make over the model.

### Installing

- Composer
Execute the following command to get the latest version of the package:
```
composer require whtht/perfectly-cache
```

In your `config/app.php` add `Whtht\PerfectlyCache\PerfectlyCacheServiceProvider::class` to the end of the `providers` array:

```php
'providers' => [
    ...
    Whtht\PerfectlyCache\PerfectlyCacheServiceProvider::class,
],
```

- Publish Configuration
````bash
php artisan vendor:publish --provider="Whtht\PerfectlyCache\PerfectlyCacheServiceProvider"
````

- Use trait on your models 
    >Add this code in your models / or add just your base model

```
    use \Whtht\PerfectlyCache\Traits\PerfectCachable;
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
- Skip in Model
    >Manage your models with ``$isPerfectCachable`` variable.
```php
<?php
namespace App;

class Category extends BaseModel
{
    /* Cache disabled by this variable */
    public $isPerfectCachable = false;
}
```

## Cache Time Adjustments
You can set cache time in config (``perfectly-cache.minutes``)  
You can specify globally from the model or directly during the query as you can apply to all models by editing them from the settings.
The cache time can be edited in the query, in the model, and in the settings.

- In Config
```php
...
"minutes" => 30,

```
- In Model ``$cacheMinutes``
```php
<?php

namespace App;


class Module extends BaseModel
{
    protected $table = "modules";

    public $cacheMinutes = 20; // Now cache time 20 minutes.
}
```

- In Query ``->remember(:minutes)``

```php
$modules = \App\Module::remember(10)->select("id", "name")->get();
```
This query will be cached for 10 minutes.

## Usage

````php
// Basic cache
$results = \App\Category::find($id);

// Basic cache skip
$results = \App\Category::skipCache()->find($id);

// Basic usage with eager load
$results = \App\Category::with("_list_category_tags")->find($id);

// Basic cache skip usage with eager load
$results = \App\Category::with("^_list_category_tags")->find($id);

````

## Notice

If you already used time on your queries and this query will be cached, like this,
```php
$modules2 = Module::select("id", "name")
    ->where("created_time_unix", ">=", time())
    ->get();
```

You need to be add ``->skipCache()`` method on this query.   
Because: This query will create a different cache each time it runs.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details
