<?php

declare(strict_types=1);

namespace App\Http\Livewire\Abstracts;

use App\Enums\SortDirection;
use App\Http\Livewire\Concerns\HasTableFilter;
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
 * @property string $view
 * @property string $previousView
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

    use HasTableFilter {
        getFilter as getFilterTrait;
        updatedFilters as updatedFiltersTrait;
    }

    public const HAS_TABLE_SORTING = false;

    // The initial view to display when the component is mounted.
    public const INITIAL_VIEW = '';

    public array $tabQueryData = [];

    public array $savedQueryData = [];

    public array $alreadyLoadedViews = [];

    public function __get(mixed $property): mixed
    {
        $value = Arr::get($this->tabQueryData[$this->view], $property);
        if ($value !== null) {
            return $value;
        }

        return parent::__get($property);
    }

    public function mount(): void
    {
        $this->filters = static::defaultFilters();

        $resolvedView = $this->resolveView();
        foreach (array_keys($this->alreadyLoadedViews) as $view) {
            $viewConstPrefix = str_replace('-', '_', $view);

            $defaultPerPage = self::defaultPerPage($viewConstPrefix);

            $this->paginators[$view]        = $view === $resolvedView ? $this->resolvePage() : 1;
            $this->paginatorsPerPage[$view] = $view === $resolvedView ? $this->resolvePerPage($defaultPerPage) : $defaultPerPage;

            if (static::HAS_TABLE_SORTING) {
                $defaultSortKey       = self::defaultSortKey($viewConstPrefix);
                $defaultSortDirection = self::defaultSortDirection($viewConstPrefix);

                $this->sortKeys[$view]       = $view === $resolvedView ? $this->resolveSortKey($defaultSortKey) : self::defaultSortKey($viewConstPrefix);
                $this->sortDirections[$view] = $view === $resolvedView ? $this->resolveSortDirection($defaultSortDirection) : self::defaultSortDirection($viewConstPrefix);
            }

            if ($view !== $resolvedView) {
                continue;
            }

            if (! array_key_exists($view, $this->filters)) {
                continue;
            }

            $this->filters[$view] = $this->resolveFilters($this->filters[$view], 'validators');
        }
    }

    public function getFilter(string $filter, string $name = 'default'): ?bool
    {
        return $this->getFilterTrait($filter, $this->view);
    }

    public function getIsAllSelectedProperty(): bool
    {
        return $this->hasAllSelectedFilters($this->filters[$this->view]);
    }

    public function updatedPaginators(int $value, string $key): void
    {
        $this->setTabbedArrayValue('paginators.'.$key, $value);
    }

    public function updatedPaginatorsPerPage(int $value, string $key): void
    {
        $this->setTabbedArrayValue('paginatorsPerPage.'.$key, $value);
    }

    public function updatedSortKeys(string $value, string $key): void
    {
        $this->setTabbedArrayValue('sortKeys.'.$key, $value);
    }

    public function updatedSortDirections(SortDirection $value, string $key): void
    {
        $this->setTabbedArrayValue('sortDirections.'.$key, $value);
    }

    public function updatedFilters(bool $value, string $key): void
    {
        $this->updatedFiltersTrait();

        $this->setTabbedArrayValue('filters.'.$key, $value);
    }

    public function queryStringHasTableSorting(): array
    {
        if (! static::HAS_TABLE_SORTING) {
            return [];
        }

        return [
            'sortKeys.default'       => ['as' => 'sort', 'except' => static::defaultSortKey($this->view)],
            'sortDirections.default' => ['as' => 'sort-direction', 'except' => static::defaultSortDirection($this->view)->value],
        ];
    }

    public function queryString(): array
    {
        return [
            'view' => ['except' => constant(static::class.'::INITIAL_VIEW'), 'history' => true],
        ];
    }

    // @phpstan-ignore-next-line - ignoring $name as we are not using it in this override.
    public function setPage(int $page, string $name = 'page'): void
    {
        $this->setPageTrait($page, $this->view);
    }

    // @phpstan-ignore-next-line - ignoring $name as we are not using it in this override.
    final public function setPerPage(int $perPage, string $name = 'default'): void
    {
        $this->setPerPageTrait($perPage, $this->view);
    }

    // @phpstan-ignore-next-line - ignoring $name as we are not using it in this override.
    public function sortBy(string $sortKey, string $name = 'default'): void
    {
        $this->sortByTrait($sortKey, $this->view);
    }

    public function getIsReadyProperty(): bool
    {
        if (! property_exists($this, Str::camel($this->view).'IsReady')) {
            return false;
        }

        // @phpstan-ignore-next-line - ignoring as it's a dynamic method name depending on the view.
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
            // @phpstan-ignore-next-line - ignoring as it's a dynamic method name depending on the view.
            return $this->{$this->view.'PerPageOptions'}();
        }

        return self::perPageOptionsTrait();
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

        $this->alreadyLoadedViews[$view] = true;

        $this->dispatch('set'.Str::studly($view).'Ready');
    }

    public function updatingView(string $newView): void
    {
        if (! array_key_exists($newView, $this->alreadyLoadedViews)) {
            $newView = static::INITIAL_VIEW;
        }

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
        if (! array_key_exists($this->view, $this->alreadyLoadedViews)) {
            $this->view = static::INITIAL_VIEW;

            return;
        }

        if (array_key_exists($this->view, $this->savedQueryData)) {
            /** @var string $key */
            foreach ($this->savedQueryData[$this->view] as $key => $value) {
                $this->syncInput($key, $value);
            }
        }
    }

    protected function resolveView(): string
    {
        $view = request()->get('view', $this->view);
        if (array_key_exists($view, $this->alreadyLoadedViews)) {
            return $view;
        }

        $this->view = static::INITIAL_VIEW;

        return static::INITIAL_VIEW;
    }

    private function setTabbedArrayValue(string $key, mixed $value): void
    {
        if (array_key_exists($key, $this->tabQueryData[$this->view])) {
            $this->tabQueryData[$this->view][$key] = $value;
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
            } elseif (Arr::has($queryStringData, $key)) {
                $except = Arr::get($queryStringData, $key)['except'];
            } else {
                continue;
            }

            if (in_array($key, ['paginators.'.$this->view, 'paginatorsPerPage.'.$this->view], true)) {
                $except = (int) $except;
            } elseif (str_starts_with($key, 'filters.'.$this->view)) {
                $except = $except === 'true' || $except === '1' || $except === true;
            }

            $this->syncInput($key, $except);
        }

        $this->triggerViewIsReady($newView);
    }
}
