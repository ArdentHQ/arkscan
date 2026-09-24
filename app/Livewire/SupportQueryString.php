<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Support\Collection;
use Livewire\Features\SupportQueryString\SupportQueryString as Base;

final class SupportQueryString extends Base
{
    /**
     * Merges Query String from livewire requests.
     *
     * Based on \Livewire\Features\SupportQueryString\SupportQueryString#getQueryString
     *
     * @return void
     */
    public function mergeQueryStringWithRequest(): void
    {
        $requestQueryData = request()->query();

        Collection::make($requestQueryData)
            ->each(function ($value, $key) {
                $this->component->syncInput($key, $value);
            });
    }
}
