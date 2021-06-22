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

- Publish Configuration

```
php artisan vendor:publish --provider="Whtht\PerfectlyCache\Providers\PerfectlyCacheServiceProvider"
```

- Use trait on your models   
    add this code in your models / or add just your base model like this   
```php
<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

use Whtht\PerfectlyCache\Traits\PerfectlyCachable;

class User extends Model
{
    use PerfectlyCachable;
}

```

All database queries you make through this model will be cached and will be read from the cache instead of the database when needed.

## Configuration
```php
// config('perfectly-cache.(name)')
// Eq: config('perfecyly-cache.enabled')
return [

    "enabled" => true, // Is cache enabled? cache for production --> !env('APP_DEBUG', false)

    "minutes" => 1, // Cache minutes.

    /**
     * If this event is triggered on this model,
     * the cache of that table is deleted.
     */
    "clear_events" => [
        "created",
        "updated",
        "deleted"
    ],
];

```
## Usage

```php
// Basic cache
$results = \App\Category::find($id);

// Basic cache skip
$results = \App\Category::skipCache()->find($id);

// Basic usage with eager load
$results = \App\Category::with("_list_category_tags")->find($id);

// Basic cache skip usage with eager load
$results = \App\Category::with("^_list_category_tags")->find($id);

```
## Cache Skipping
- With Chain  
```php
    // ->skipCache();
    $result = Category::select("id", "name")->skipCache()->get();
```
- With Eager Load   
```php
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
    >Manage your models with ``$isCacheEnable`` variable.
```php
<?php
namespace App;

class Category extends BaseModel
{
    /* Cache disabled by this variable */
    protected $isCacheEnable = false;
}
```

## Cache Time Adjustments
You can set cache time in config (``perfectly-cache.minutes``)  
You can specify globally from the model or directly during the query as you can apply to all models by editing them from the settings.
The cache time can be edited in the query, in the model, and in the settings.

- In Config
```
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

    protected $cacheMinutes = 20; // Now cache time 20 minutes.
}
```

- In Query ``->remember(:minutes)``

```php
$modules = \App\Module::remember(10)->select("id", "name")->get();
```
This query will be cached for 10 minutes.

- In Eager Load
```php
$modules = \App\Module::with([
    "(10)categories:id,name,module_id"
])->select("id", "name")->get();
// Categories will be cached for 10 minutes.
```


## Programmatically Cache Reloading
If you want to refresh the query logically, you can use `` ->reloadCache() `` as follows.
```php
$module = Module::select("id", "name", "need_cache_reload")->first();
if($module->need_cache_reload) { // simple true value
    $module->reloadCache();
}
```

## Commands
```bash
# Clear all caches.
php artisan perfectly-cache:clear

#Clear all users table caches
php artisan perfectly-cache:clear users

#Clear all users and modules tables caches
php artisan perfectly-cache:clear users modules
# Infinity table names

#Show cache details
php artisan perfectly-cache:list
```

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
