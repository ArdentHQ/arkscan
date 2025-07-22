<?php

declare(strict_types=1);

namespace App\Http\Livewire\Abstracts;

use App\Enums\SortDirection;
use App\Http\Livewire\Concerns\HasTablePagination;
use App\Http\Livewire\Concerns\HasTableSorting;
use App\Http\Livewire\Concerns\SyncsInput;
use App\Livewire\SupportQueryString;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\Component;

/**
 * @property int $page
 * @property ?int $perPage
 */
abstract class TabbedComponent extends Component
{
    use SyncsInput;
    use HasTablePagination {
        setPage as setPageTrait;
        setPerPage as setPerPageTrait;
        perPageOptions as perPageOptionsTrait;
    }
    use HasTableSorting {
        sortBy as sortByTrait;
    }

    public array $tabQueryData = [];

    public array $savedQueryData = [];

    public function mount(): void
    {
        $resolvedView = $this->resolveView();
        foreach (array_keys($this->alreadyLoadedViews) as $view) {
            $viewConstPrefix = str_replace('-', '_', $view);

            $defaultPerPage = self::defaultPerPage($viewConstPrefix);
            $defaultSortKey = self::defaultSortKey($viewConstPrefix);
            $defaultSortDirection = self::defaultSortDirection($viewConstPrefix);

            $this->paginators[$view] = $view === $resolvedView ? $this->resolvePage() : 1;
            $this->paginatorsPerPage[$view] = $view === $resolvedView ? $this->resolvePerPage($defaultPerPage) : $defaultPerPage;
            $this->sortKeys[$view] = $view === $resolvedView ? $this->resolveSortKey($defaultSortKey) : self::defaultSortKey($viewConstPrefix);
            $this->sortDirections[$view] = $view === $resolvedView ? $this->resolveSortDirection($defaultSortDirection) : self::defaultSortDirection($viewConstPrefix);

            if ($view === $resolvedView) {
                // $this->setPaginatorsPerPage($this->paginatorsPerPage[$view], $view);
                // $this->setSortKeys($this->sortKeys[$view], $view);
                // $this->setSortDirections($this->sortDirections[$view], $view);
            }
        }
    }

    public function getFilterProperty(): array
    {
        if (! array_key_exists($this->view, $this->filters)) {
            return [];
        }

        return $this->filters[$this->view];
    }

    public function updatedPaginators(int $value, $key): void
    {
        $this->setTabbedArrayValue('paginators.'.$key, $value);
    }

    public function updatedPaginatorsPerPage(int $value, $key): void
    {
        $this->setTabbedArrayValue('paginatorsPerPage.'.$key, $value);
    }

    public function updatedSortKeys(string $value, $key): void
    {
        $this->setTabbedArrayValue('sortKeys.'.$key, $value);
    }

    public function updatedSortDirections(SortDirection $value, $key): void
    {
        $this->setTabbedArrayValue('sortDirections.'.$key, $value);
    }

    private function setTabbedArrayValue(string $key, mixed $value): void
    {
        if (array_key_exists($key, $this->tabQueryData[$this->view])) {
            $this->tabQueryData[$this->view][$key] = $value;
        }
    }

    public function __get(mixed $property): mixed
    {
        $value = Arr::get($this->tabQueryData[$this->view], $property);
        if ($value !== null) {
            return $value;
        }

        if (array_key_exists($property, $this->tabQueryData[$this->view])) {
            return $this->tabQueryData[$this->view][$property];
        }

        $value = Arr::get($this->tabQueryData[$this->previousView], $property);
        if ($value !== null) {
            return $value;
        }

        if (array_key_exists($property, $this->tabQueryData[$this->previousView])) {
            return $this->tabQueryData[$this->previousView][$property];
        }

        return parent::__get($property);
    }

    public function __set(string $property, mixed $value): void
    {
        if (Arr::has($this->tabQueryData[$this->view], $property)) {
            $this->tabQueryData[$this->view][$property] = $value;
        }
    }

    public function queryStringHasTableSorting(): array
    {
        $queryString = [
            'sortKeys.default' => ['as' => 'sort', 'except' => static::defaultSortKey($this->view)],
            'sortDirections.default' => ['as' => 'sort-direction', 'except' => static::defaultSortDirection($this->view)->value],
        ];

        return $queryString;
    }

    public function queryString(): array
    {
        return [
            'view' => ['except' => constant(static::class.'::INITIAL_VIEW'), 'history' => true],
        ];
    }

    public function setPage(int $page, string $name = 'page'): void
    {
        $this->setPageTrait($page, $this->view);
    }

    final public function setPerPage(int $perPage, string $name = 'default'): void
    {
        $this->setPerPageTrait($perPage, $this->view);
    }

    public function sortBy(string $sortKey, string $name = 'default'): void
    {
        $this->sortByTrait($sortKey, $this->view);
    }

    public function getIsReadyProperty(): bool
    {
        if (! property_exists($this, Str::camel($this->view).'IsReady')) {
            return false;
        }

        return $this->{Str::camel($this->view).'IsReady'} ?? false;
    }

    public function getPageProperty(): int
    {
        return $this->getPage($this->view);
    }

    public function getPerPageProperty(): int
    {
        return $this->getPerPage($this->view);
    }

    public function getSortKeyProperty(): string
    {
        return $this->getSortKey($this->view);
    }

    public function getSortDirectionProperty(): SortDirection
    {
        return $this->getSortDirection($this->view);
    }

    public function perPageOptions(): array
    {
        if (method_exists($this, $this->view.'PerPageOptions')) {
            return $this->{$this->view.'PerPageOptions'}();
        }

        return $this->perPageOptionsTrait();
    }

    public function getNoResultsMessageProperty(): null|string
    {
        throw new \Exception('Base getNoResultsMessageProperty not implemented');
    }

    public function triggerViewIsReady(?string $view = null): void
    {
        if (! array_key_exists($this->view, $this->savedQueryData)) {
            $this->saveViewData($view);
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
            $options = [];
            if (method_exists($this, $this->view.'PerPageOptions')) {
                $options = $this->{$this->view.'PerPageOptions'}();
            } else {
                $options = $this->perPageOptions();
            }

            $perPage = (int) $this->tabQueryData[$this->view]['paginatorsPerPage.'.$this->view];
            if (! in_array($perPage, $options, true)) {
                $this->tabQueryData[$this->view]['paginatorsPerPage.'.$this->view] = self::defaultPerPage($this->view);
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
            /** @var string $key */
            foreach ($this->savedQueryData[$this->view] as $key => $value) {
                $this->syncInput($key, $value);
            }
        }
    }

    private function saveViewData(?string $newView = null): void
    {
        $queryStringSupport = new SupportQueryString();
        $queryStringSupport->setComponent($this);
        $queryStringSupport->mergeQueryStringWithRequest();

        $this->savedQueryData[$this->view] = $this->tabQueryData[$this->view];

        if ($newView === null) {
            return;
        }

        // Reset the querystring data on view change to clear the URL
        $queryStringData = $queryStringSupport->getQueryString();

        $properties = $this->getAttributesByName();

        /** @var string $key */
        foreach (array_keys($this->tabQueryData[$this->view]) as $key) {
            $except = null;

            $property = $properties->get($key);
            if ($property !== null) {
                $except = $property->except;
            } else if (Arr::has($queryStringData, $key.'.except')) {
                $except = Arr::get($queryStringData, $key.'.except');
            } else if (Arr::has($queryStringData, $key)) {
                $except = Arr::get($queryStringData, $key)['except'];
            } else {
                continue;
            }

            if (in_array($key, ['paginators.page', 'paginatorsPerPage.page'], true)) {
                $except = (int) $except;
            }

            $this->syncInput($key, $except);
        }

        $this->triggerViewIsReady($newView);
    }

    protected function resolveView(): string
    {
        return request()->get('view', $this->view);
    }
}
