<?php
/**
 * Created by PhpStorm.
 * User: Musa
 * Date: 24.03.2019
 * Time: 14:07
 */

namespace Whtht\PerfectlyCache\Builders;


use Illuminate\Database\Eloquent\Builder;

class EloquentBuilder extends Builder
{
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
     * @param int $cacheMinutes
     * @return array
     */
    protected function eagerLoadRelation(array $models, $name, \Closure $constraints, $skipCache = false, $cacheMinutes = 0)
    {
        $relation = $this->getRelation($name);

        if($relation->getQuery() instanceof EloquentBuilder) {

            if ($skipCache) {
                $relation->getQuery()->skipCache($skipCache);
            } else if ($cacheMinutes) {
                $relation->getQuery()->getQuery()->remember($cacheMinutes);
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
