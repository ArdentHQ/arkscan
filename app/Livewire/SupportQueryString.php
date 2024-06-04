<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Livewire\Features\SupportQueryString\BaseUrl;
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
        /** @var Arrayable<(int|string), mixed>|iterable<(int|string), mixed>|null $requestQueryData */
        $requestQueryData = request()->query();

        $requestQuery = Collection::make($requestQueryData);

        /** @var array $componentQueryString */
        $componentQueryString = $this->component->queryString();

        Collection::make($componentQueryString)
            ->mapWithKeys(function ($value, $key) {
                $key     = is_string($key) ? $key : $value;
                $alias   = $value['as'] ?? $key;
                $history = $value['history'] ?? true;
                $keep    = $value['alwaysShow'] ?? $value['keep'] ?? false;
                $except  = $value['except'] ?? null;

                $baseUrl = new BaseUrl(as: $alias, history: $history, keep: $keep, except: $except);

                return [$key => $baseUrl->getFromUrlQueryString($alias, $except)];
            })
            ->merge($requestQuery)
            ->each(function ($value, $key) {
                // @phpstan-ignore-next-line
                $this->component->{$key} = $value;
            });
    }
}
