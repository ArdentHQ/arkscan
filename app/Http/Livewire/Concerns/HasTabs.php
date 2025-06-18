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
    use HasTablePagination;

    public array $tabQueryData = [];

    public array $savedQueryData = [];

    public function __get(mixed $property): mixed
    {
        // dump($property);

        Log::debug('__get', [
            'property' => $property,
        ]);

        $value = Arr::get($this->tabQueryData[$this->view], $property);
        if ($value !== null) {
            return $value;
        }

        $value = Arr::get($this->tabQueryData[$this->previousView], $property);
        if ($value !== null) {
            return $value;
        }

        return parent::__get($property);
    }

    public function __set(string $property, mixed $value): void
    {
        Log::debug('__set', [
            'property' => $property,
            'value'    => $value,
        ]);
        if (Arr::has($this->tabQueryData[$this->view], $property)) {
            $this->tabQueryData[$this->view][$property] = $value;
        }
    }

    public function triggerViewIsReady(?string $view = null): void
    {
        Log::debug('triggerViewIsReady', [
            'view' => $view,
            'curView' => $this->view,
        ]);
        // dump('asd');
        if (! array_key_exists($this->view, $this->savedQueryData)) {
            $this->saveViewData();
        }

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

            Log::debug('triggerViewIsReady validate perPage', [
                'value'    => $this->tabQueryData[$this->view]['perPage'],
            ]);
        }

        $this->dispatch('set'.Str::studly($view).'Ready');

        $this->alreadyLoadedViews[$view] = true;
    }

    public function updatingView(string $newView): void
    {
        Log::debug('updatingView', [
            'newView' => $newView,
            'previousView' => $this->previousView,
        ]);
        // dump('addsd');
        if ($newView === $this->view) {
            return;
        }

        $this->previousView = $this->view;

        $this->saveViewData($newView);
    }

    /**
     * Apply existing view data to query string.
     *
     * @return void
     */
    public function updatedView(): void
    {
        Log::debug('updatedView', [
            'view' => $this->view,
        ]);
        // dump('ss');
        if (array_key_exists($this->view, $this->savedQueryData)) {
            /** @var string $key */
            foreach ($this->savedQueryData[$this->view] as $key => $value) {
                if ($key === 'paginators') {
                    $this->setPage($value['page']);
                }

                $this->syncInput($key, $value);
            }
        }
    }

    abstract private function tabbedComponent();

    private function saveViewData(?string $newView = null): void
    {
        Log::debug('saveViewData', [
            'view' => $this->view,
            'newView' => $newView,
        ]);
        // dump('xx');
        $queryStringSupport = new SupportQueryString();
        $queryStringSupport->setComponent($this);
        $queryStringSupport->mergeQueryStringWithRequest();

        $this->savedQueryData[$this->view] = $this->tabQueryData[$this->view];

        // if ($newView === null) {
        //     return;
        // }

        // Reset the querystring data on view change to clear the URL
        $queryStringData = $queryStringSupport->getQueryString();

        $properties = $this->getAttributesByName();

        /** @var string $key */
        foreach (array_keys($this->tabQueryData[$this->view]) as $key) {
            Log::debug('saveViewData loop', [
                'key' => $key,
            ]);

            $except = null;

            if ($key === 'paginators') {
                $key = 'paginators.page';
            }

            $property = $properties->get($key);
            if ($property !== null) {
                $except = $property->except;
            } else {
                $except = $queryStringData[$key]['except'];
            }

            if ($key === 'paginators.page') {
                Log::debug('saveViewData setPage', [
                    'except' => $except,
                ]);

                $this->setPage($except);

                continue;
            }

            $this->syncInput($key, $except);
        }

        $this->triggerViewIsReady($newView);
    }

    private function resolveView(): string
    {
        Log::debug('resolveView', [
            'view' => $this->view,
        ]);
        // dump('yy');
        return request()->get('view', $this->view);
    }

    private function resolvePage(): int
    {
        Log::debug('resolvePage', [
            'page' => $this->getPage(),
        ]);
        // dump('zz');
        return (int) request()->get('page', $this->getPage());
    }

    private function resolvePerPage(): ?int
    {
        Log::debug('resolvePerPage', [
            'perPage' => $this->perPage,
        ]);
        // dump('asdsdsdsd');
        $value = request()->get('perPage', $this->perPage);

        return $value === null ? null : (int) $value;
    }
}
