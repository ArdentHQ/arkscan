<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Features\SupportBrowserHistory;

class SupportBrowserHistoryWrapper extends SupportBrowserHistory
{
    public function mergeQueryStringWithComponent(mixed $component): void
    {
        if (! $this->mergedQueryParamsFromDehydratedComponents) {
            $this->mergedQueryParamsFromDehydratedComponents = collect($this->getExistingQueryParams());
        }

        $excepts = $this->getExceptsFromComponent($component);

        $this->mergedQueryParamsFromDehydratedComponents = collect(request()->query())
            ->merge($this->getQueryParamsFromComponentProperties($component))
            ->merge($this->mergedQueryParamsFromDehydratedComponents)
            ->reject(function ($value, $key) use ($excepts) {
                // dump($value, $key);
                return isset($excepts[$key]) && $excepts[$key] === $value;
            })
            ->each(function ($property, $key) use ($component): void {
                $component->{$key} = $property;
            });
        // ->map(function ($property) {
            //     return is_bool($property) ? json_encode($property) : $property;
        // });

        // return $this->mergedQueryParamsFromDehydratedComponents;

        // return self::init()->mergeComponentPropertiesWithExistingQueryParamsFromOtherComponentsAndTheRequest($component);
    }
}
