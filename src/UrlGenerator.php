<?php namespace Lanin\Laravel\Hashids;

use Illuminate\Contracts\Routing\UrlRoutable;

class UrlGenerator extends \Illuminate\Routing\UrlGenerator
{
    /**
     * Replace UrlRoutable parameters with their route parameter.
     *
     * @param  array  $parameters
     * @return array
     */
    protected function replaceRoutableParameters($parameters = [])
    {
        $parameters = is_array($parameters) ? $parameters : [$parameters];

        foreach ($parameters as $key => $parameter)
        {
            if ($parameter instanceof UrlRoutable)
            {
                $id = $parameter->getRouteKey();

                // Encode id if model uses hashids
                if ( ! ($parameter instanceof DoNotUseHashids))
                {
                    $id = Hashids::fromModel($parameter)->encode($id);
                }

                $parameters[$key] = $id;
            }
        }

        return $parameters;
    }
}