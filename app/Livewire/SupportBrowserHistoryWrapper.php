<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Contracts\Support\Arrayable;
use Livewire\Features\SupportBrowserHistory;

final class SupportBrowserHistoryWrapper extends SupportBrowserHistory
{
    /**
     * Merges Query String from livewire requests.
     *
     * Based on SupportBrowserHistory#mergeComponentPropertiesWithExistingQueryParamsFromOtherComponentsAndTheRequest
     *
     * @param mixed $component
     * @return void
     */
    public function mergeRequestQueryStringWithComponent(mixed $component): void
    {
        if (! $this->mergedQueryParamsFromDehydratedComponents) {
            // @phpstan-ignore-next-line
            $this->mergedQueryParamsFromDehydratedComponents = collect($this->getExistingQueryParams());
        }

        $excepts = $this->getExceptsFromComponent($component);

        /** @var Arrayable<(int|string), mixed>|iterable<(int|string), mixed>|null $requestQuery */
        $requestQuery = request()->query();

        $this->mergedQueryParamsFromDehydratedComponents = collect($requestQuery)
            ->merge($this->getQueryParamsFromComponentProperties($component))
            ->merge($this->mergedQueryParamsFromDehydratedComponents)
            ->reject(function ($value, $key) use ($excepts) {
                return array_key_exists($key, $excepts->toArray()) && $excepts[$key] === $value;
            })
            ->each(function ($property, $key) use ($component): void {
                // @phpstan-ignore-next-line
                $component->{$key} = $property;
            });
    }
}
