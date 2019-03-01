<?php
/**
 * Created by PhpStorm.
 * User: Musa
 * Date: 31.01.2019
 * Time: 18:33
 */

namespace Whtht\PerfectlyCache\Builders;


use Whtht\PerfectlyCache\Exceptions\TraitNotUsedException;
use Whtht\PerfectlyCache\Traits\BuildsQueries;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class EloquentBuilder extends Builder
{

    use BuildsQueries;
    /**
     * @var array
     */
    protected $eagerLoad = [];

    /**
     * @param array $models
     * @return array
     * @throws TraitNotUsedException
     */
    public function eagerLoadRelations(array $models)
    {

        foreach ($this->eagerLoad as $name => $constraints) {

            $skipCache = false;
            $cacheMinutes = 0;

            if(substr($name, 0, 1) == "^") {
                $name = substr($name, 1, strlen($name));
                $skipCache = true;
            }

            preg_match('/\(+[0-9]+\)/', $name, $match);
            if(count($match)) {
                $cacheMinutes = (int)str_replace(['(', ')'], '', $match[0]);

                $name = str_replace($match[0], '', $name);
            }

            if (strpos($name, '.') === false) {
                $models = $this->eagerLoadRelation($models, $name, $constraints, $skipCache, $cacheMinutes);
            }
        }

        return $models;
    }

    /**
     * @param array $models
     * @param string $name
     * @param \Closure $constraints
     * @param bool $skipCache
     * @return array
     * @throws TraitNotUsedException
     */
    protected function eagerLoadRelation(array $models, $name, \Closure $constraints, $skipCache = false, $cacheMinutes = 0)
    {
        $relation = $this->getRelation($name);

//        if(config('perfectly-cache.debug', false) && ! ($relation->getQuery()) instanceof EloquentBuilder) {
//            //Trait not used exception
//            throw (new TraitNotUsedException($relation->getQuery()));
//        }

        if($relation->getQuery() instanceof EloquentBuilder) {
            $relation->getQuery()->skipCache($skipCache);
            if($cacheMinutes) {
                $relation->getQuery()->remember($cacheMinutes);
            }
        }

        $relation->addEagerConstraints($models);

        $constraints($relation);

        return $relation->match(
            $relation->initRelation($models, $name),
            $relation->getEager(), $name
        );
    }
}
