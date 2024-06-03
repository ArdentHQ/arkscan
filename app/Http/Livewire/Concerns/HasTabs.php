<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use App\Livewire\SupportBrowserHistoryWrapper;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait HasTabs
{
    use HasTablePagination;

    public array $tabQueryData = [];

    public array $savedQueryData = [];

    public function __get(mixed $property): mixed
    {
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
        if (Arr::has($this->tabQueryData[$this->view], $property)) {
            $this->tabQueryData[$this->view][$property] = $value;
        }
    }

    public function triggerViewIsReady(?string $view = null): void
    {
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
        }

        $this->dispatch('set'.Str::studly($view).'Ready');

        $this->alreadyLoadedViews[$view] = true;
    }

    public function updatingView(string $newView): void
    {
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
        if (array_key_exists($this->view, $this->savedQueryData)) {
            foreach ($this->savedQueryData[$this->view] as $key => $value) {
                // @phpstan-ignore-next-line
                $this->{$key} = $value;
            }
        }
    }

    abstract private function tabbedComponent();

    private function saveViewData(?string $newView = null): void
    {
        SupportBrowserHistoryWrapper::init()->mergeRequestQueryStringWithComponent($this);

        $this->savedQueryData[$this->view] = $this->tabQueryData[$this->view];

        if ($newView === null) {
            return;
        }

        // Reset the querystring data on view change to clear the URL
        $queryStringData = $this->queryString();
        foreach ($this->tabQueryData[$this->view] as $key => $value) {
            if ($key === 'page') {
                $this->setPage(1);

                continue;
            }

            // @phpstan-ignore-next-line
            $this->{$key} = $queryStringData[$key]['except'];
        }

        $this->triggerViewIsReady($newView);
    }

    private function resolveView(): string
    {
        return request()->get('view', $this->view);
    }

    private function resolvePage(): int
    {
        return (int) request()->get('page', $this->getPage());
    }

    private function resolvePerPage(): int
    {
        return (int) request()->get('perPage', $this->perPage);
    }
}
