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

            if(substr($name, 0, 1) == "^") {
                $name = substr($name, 1, strlen($name));
                $skipCache = true;
            }

            if (strpos($name, '.') === false) {
                $models = $this->eagerLoadRelation($models, $name, $constraints, $skipCache);
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
    protected function eagerLoadRelation(array $models, $name, \Closure $constraints, $skipCache = false)
    {
        $relation = $this->getRelation($name);

        if ( ! ($relation->getQuery()) instanceof EloquentBuilder) {
            //Trait not used exception
            throw (new TraitNotUsedException($relation->getQuery()));

        } else {
            $relation->getQuery()->skipCache($skipCache);
        }

        $relation->addEagerConstraints($models);

        $constraints($relation);
        return $relation->match(
            $relation->initRelation($models, $name),
            $relation->getEager(), $name
        );
    }
}