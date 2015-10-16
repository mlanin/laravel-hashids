<?php namespace Lanin\Laravel\Hashids;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Router extends \Illuminate\Routing\Router
{
    /**
     * Register a model binder for a wildcard.
     *
     * @param  string  $key
     * @param  string  $class
     * @param  \Closure|null  $callback
     * @return void
     *
     * @throws NotFoundHttpException
     */
    public function model($key, $class, \Closure $callback = null)
    {
        $this->bind($key, function ($value) use ($class, $callback) {
            if (is_null($value)) {
                return;
            }

            // For model binders, we will attempt to retrieve the models using the first
            // method on the model instance. If we cannot retrieve the models we'll
            // throw a not found exception otherwise we will return the instance.
            $instance = $this->container->make($class);

            // Try to decode hashid
            $decoded = Hashids::fromModel($instance)->decode($value);

            // If not decoded, use default value
            $value = ! empty($decoded) ? $decoded : $value;

            if ($model = $instance->where($instance->getRouteKeyName(), $value)->first()) {
                return $model;
            }

            // If a callback was supplied to the method we will call that to determine
            // what we should do when the model is not found. This just gives these
            // developer a little greater flexibility to decide what will happen.
            if ($callback instanceof \Closure) {
                return call_user_func($callback, $value);
            }

            throw new NotFoundHttpException;
        });
    }
}