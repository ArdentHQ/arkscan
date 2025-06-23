<?php

namespace App\Http\Livewire\Concerns;

use Livewire\ComponentHookRegistry;
use Livewire\Features\SupportQueryString\SupportQueryString;

trait IsTabbed
{
    use SyncsInput;

    public array $savedQueryStrings = [];

    abstract public function queryString(): array;

    public function populateQueryStrings(): void
    {
        foreach ($this->savedQueryStrings as $key => $value) {
            $this->syncInput($key, $value);
        }
    }

    public function hideQueryStrings(): void
    {
        $supportQueryStringHook = ComponentHookRegistry::getHook($this, SupportQueryString::class);

        $queryStringData = $supportQueryStringHook->getQueryString();

        foreach ($queryStringData as $key => $queryStringData) {
            $value = data_get($this, $key) ?? null;

            $this->savedQueryStrings[$key] = $value;

            $this->syncInput($key, $queryStringData['except'] ?? null);
        }
    }
}
