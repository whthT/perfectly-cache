<?php
/**
 * Created by PhpStorm.
 * User: Musa
 * Date: 1.02.2019
 * Time: 10:57
 */

namespace Whtht\PerfectlyCache\Exceptions;


use Throwable;
use Illuminate\Database\Query\Builder;

class TraitNotUsedException extends \Exception
{
    protected $model;
    public function __construct($builder)
    {

        parent::__construct();
        try {

            $this->model = get_class($builder->getModel());

            $this->message = "$this->model Model has no PerfectCachable Trait! Please check all models using by present query.";
        }catch (\Exception $exception) {
            $this->message = "Some models has not PerfectCachable Trait! Please check all models using by present query.";
        }

        $this->report();
    }

    public function report()
    {
        echo $this->message;
    }

}