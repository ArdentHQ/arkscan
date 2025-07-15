<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use App\Livewire\SupportQueryString;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait HasTabs
{
    use SyncsInput;
    // use HasTablePagination;

    public array $tabQueryData = [];

    public array $savedQueryData = [];

    public function __get(mixed $property)
    {
        // if ($property === 'paginators.page') {
        //     $value = $this->tabQueryData[$this->view]['paginators.page'] ?? null;
        //     if ($value !== null) {
        //         return $value;
        //     }
        // }

        $value = Arr::get($this->tabQueryData[$this->view], $property);
        if ($value !== null) {
            return $value;
        }

        // $value = Arr::get($this->tabQueryData[$this->previousView], $property);
        // if ($value !== null) {
        //     return $value;
        // }

        // return parent::__get($property);
    }

    public function __set(string $property, mixed $value): void
    {
        if (Arr::has($this->tabQueryData[$this->view], $property)) {
            $this->tabQueryData[$this->view][$property] = $value;
        }
    }

    public function triggerViewIsReady(?string $view = null): void
    {
        // if (! array_key_exists($this->view, $this->savedQueryData)) {
        //     $this->saveViewData();
        // }

        if ($view === null) {
            $view = $this->view;
        }

        if (! array_key_exists($view, $this->alreadyLoadedViews)) {
            return;
        }

        if ($this->alreadyLoadedViews[$view] === true) {
            return;
        }

        if (array_key_exists('perPage', $this->tabQueryData[$this->view])) {
            $component = $this->tabbedComponent();

            $perPage = (int) $this->tabQueryData[$this->view]['perPage'];
            if (! in_array($perPage, $component::perPageOptions(), true)) {
                $this->tabQueryData[$this->view]['perPage'] = $component::defaultPerPage();
            }
        }

        $this->alreadyLoadedViews[$view] = true;

        $this->dispatch('set'.Str::studly($view).'Ready');
    }

    public function updatingView(string $newView): void
    {
        if ($newView === $this->view) {
            return;
        }

        $this->saveViewData($newView);
        $this->loadViewData($newView);

        $this->previousView = $this->view;
    }

    /**
     * Apply existing view data to query string.
     *
     * @return void
     */
    public function updatedView(): void
    {
        Log::debug('updatedView');
        // $this->updateViewData();
    }

    public function hasLoadedView(string $view): bool
    {
        if (! array_key_exists($view, $this->alreadyLoadedViews)) {
            return false;
        }

        return $this->alreadyLoadedViews[$view];
    }

    public function updateViewData(): void
    {
        $queryData = [
            ...$this->tabQueryData[$this->view] ?? [],
            ...$this->savedQueryData[$this->view] ?? [],
        ];

        foreach ($queryData as $key => $value) {
            // if ($key === 'paginators.page') {
            //     $this->gotoPage($value, false);

            //     // continue;
            // } else if ($key === 'perPage') {
            //     $this->setPerPage($value);

            //     // continue;
            // }

            Log::debug('Updating view data', [
                'view'  => $this->view,
                'key'   => $key,
                'value' => $value,
            ]);

            $this->syncInput($key, $value);
        }
    }

    public function gotoPage(int $page, bool $emitEvent = true): void
    {
        if ($emitEvent) {
            $this->dispatch('pageChanged');
        }

        // $this->setPage($page);

        $this->tabQueryData[$this->view]['paginators.page'] = $page;
    }

    // private function resolvePage(): int
    // {
    //     return (int) request()->get('page', $this->getPage());
    // }

    // private function resolvePerPage(): ?int
    // {
    //     $value = request()->get('perPage', $this->perPage);

    //     return $value === null ? null : (int) $value;
    // }

    public function hydrateHasTabs(): void
    {
        if (! array_key_exists($this->view, $this->tabQueryData)) {
            return;
        }

        Log::debug('hydrateHasTabs');
        $this->updateViewData();

        // $query = $this->tabQueryData[$this->view];

        // $this->perPage = $query['perPage'];
        // // $this->syncInput('perPage', $query['perPage']);
        // $this->sortKey = $query['sortKey'];
        // // $this->syncInput('sortKey', $query['sortKey']);
        // $this->sortDirection = $query['sortDirection'];
        // // $this->syncInput('sortDirection', $query['sortDirection']);

        // Log::debug('Hydrating HasTabs', [
        //     'view' => $this->view,
        //     'query' => $query,
        // ]);

        // // $this->setPerPage($query['perPage'] ?? static::resolvePerPage());
        // // $this->gotoPage($this->tabQueryData[$this->view]['paginators.page'] ?? static::resolvePage(), false);
    }

    abstract private function tabbedComponent();

    private function saveViewData(?string $newView = null): void
    {
        $queryStringSupport = new SupportQueryString();
        $queryStringSupport->setComponent($this);
        $queryStringSupport->mergeQueryStringWithRequest();

        $this->savedQueryData[$this->view] = $this->tabQueryData[$this->view];
    }

    private function loadViewData(?string $newView = null): void
    {
        $queryStringSupport = new SupportQueryString();
        $queryStringSupport->setComponent($this);
        $queryStringSupport->mergeQueryStringWithRequest();

        // Reset the querystring data on view change to clear the URL
        $queryStringData = $queryStringSupport->getQueryString();

        $properties = $this->getAttributesByName();

        $view = $newView ?? $this->view;

        Log::debug('Loading view data', [
            'view'            => $view,
            'queryStringData' => $queryStringData,
        ]);

        /** @var string $key */
        foreach (array_keys($this->tabQueryData[$view]) as $key) {
            $except = null;

            // if ($key === 'paginators') {
            //     $key = 'paginators.page';
            // }

            // if ($key === 'paginators.page') {
            //     $key = 'paginators';
            // }

            $property = $properties->get($key);
            if ($property !== null) {
                $except = $property->except;
            } elseif (Arr::get($queryStringData, $key.'.except') !== null) {
                $except = Arr::get($queryStringData, $key.'.except');
            } elseif ($queryStringData[$key]['except'] ?? null !== null) {
                $except = $queryStringData[$key]['except'];
            } else {
                Log::debug('No except found for key', [
                    'key'  => $key,
                    'view' => $view,
                ]);

                continue;
            }

            if ($key === 'paginators.page') {
                // $this->setPage($except);
                // $this->gotoPage($except, false);

                // continue;
            }

            Log::debug('Loading view data', [
                'key'    => $key,
                'except' => $except,
            ]);

            $this->syncInput($key, $except);
        }

        $this->triggerViewIsReady($newView);
    }

    private function resolveView(): string
    {
        return request()->get('view', $this->view);
    }
}
