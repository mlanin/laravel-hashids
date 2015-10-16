<?php namespace Lanin\Laravel\Hashids;

use Illuminate\Database\Eloquent\Model;

class Hashids
{
    /**
     * Construct new Hashids object.
     *
     * @param  Model|null $model
     * @throws \Exception
     * @return \Hashids\Hashids
     */
    public static function fromModel($model = null)
    {
        return new \Hashids\Hashids(self::get($model, 'salt'), self::get($model, 'length'), self::get($model, 'alphabet'));
    }

    /**
     * Get option.
     *
     * @param  Model $model
     * @param  string $var
     * @return mixed
     */
    private static function get($model, $var)
    {
        if ($model instanceof Model && ! empty($model->hashids[$var]))
        {
            return $model->hashids[$var];
        }

        $value = config('hashids.' . $var);

        if (empty($value) && $var == 'salt')
        {
            $value = env('APP_KEY');
        }

        return $value;
    }
}