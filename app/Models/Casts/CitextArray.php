<?php

declare(strict_types=1);

namespace App\Models\Casts;

use App\Services\BigNumber;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

final class CitextArray implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param Model  $model
     * @param string $key
     * @param mixed  $value
     * @param array  $attributes
     *
     * @return mixed
     */
    public function get($model, $key, $value, $attributes)
    {
        if (is_array($value)) {
            return $value;
        }

        return array_filter(explode(',', trim($value, '{}')));
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model  $model
     * @param string $key
     * @param mixed  $value
     * @param array  $attributes
     *
     * @return mixed
     */
    public function set($model, $key, $value, $attributes)
    {
        if (! is_array($value)) {
            return $value;
        }

        return sprintf(
            '{%s}',
            implode(',', $value)
        );
    }
}
