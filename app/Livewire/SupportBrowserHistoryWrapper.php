<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Features\SupportBrowserHistory;

class SupportBrowserHistoryWrapper extends SupportBrowserHistory
{
    /**
     * Merges Query String from livewire requests
     *
     * Based on SupportBrowserHistory#mergeComponentPropertiesWithExistingQueryParamsFromOtherComponentsAndTheRequest
     *
     * @param mixed $component
     * @return void
     */
    public function mergeRequestQueryStringWithComponent(mixed $component): void
    {
        if (! $this->mergedQueryParamsFromDehydratedComponents) {
            $this->mergedQueryParamsFromDehydratedComponents = collect($this->getExistingQueryParams());
        }

        $excepts = $this->getExceptsFromComponent($component);

        $this->mergedQueryParamsFromDehydratedComponents = collect(request()->query())
            ->merge($this->getQueryParamsFromComponentProperties($component))
            ->merge($this->mergedQueryParamsFromDehydratedComponents)
            ->reject(function ($value, $key) use ($excepts) {
                return isset($excepts[$key]) && $excepts[$key] === $value;
            })
            ->each(function ($property, $key) use ($component): void {
                $component->{$key} = $property;
            });
    }
}
