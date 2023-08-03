<?php

declare(strict_types=1);

namespace App\Http\Livewire\Delegates;

use App\Livewire\SupportBrowserHistoryWrapper;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\Component;

final class Tabs extends Component
{
    public string $view = 'delegates';

    public ?string $previousView = 'delegates';

    public array $tabQueryData = [];

    public array $savedQueryData = [];

    public array $alreadyLoadedViews = [
        'delegates'     => false,
        'missed-blocks' => false,
        'recent-votes'  => false,
    ];

    // /** @var mixed */
    // protected $listeners = [
    //     'showWalletView',
    // ];

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

        if (Arr::has($this->tabQueryData[$this->previousView], $property)) {
            $this->tabQueryData[$this->previousView][$property] = $value;
        }
    }

    public function queryString(): array
    {
        $params = [
            'view'    => ['except' => 'delegates'],
            'page'    => ['except' => 1],
            'perPage' => ['except' => intval(config('arkscan.pagination.per_page'))],
        ];

        // We need to pass in the filters for previous view so we can hide it from the URL
        if ($this->view !== 'delegates' && $this->previousView !== 'delegates') {
            return $params;
        }

        return [
            ...$params,

            // TODO: Filters - https://app.clickup.com/t/861n4ydmh
            // Filters
            // 'outgoing'      => ['except' => true],
            // 'incoming'      => ['except' => true],
            // 'transfers'     => ['except' => true],
            // 'votes'         => ['except' => true],
            // 'multipayments' => ['except' => true],
            // 'others'        => ['except' => true],
        ];
    }

    public function boot(): void
    {
        if ($this->tabQueryData === []) {
            $this->tabQueryData = [
                'delegates' => [
                    'page'          => 1,
                    'perPage'       => 51, //WalletTransactionTable::defaultPerPage(),

                    // TODO: Filters - https://app.clickup.com/t/861n4ydmh
                    'outgoing'      => true,
                    'incoming'      => true,
                    'transfers'     => true,
                    'votes'         => true,
                    'multipayments' => true,
                    'others'        => true,
                ],

                'missed-blocks' => [
                    'page'    => 1,
                    'perPage' => 1, //WalletBlockTable::defaultPerPage(),
                ],

                'recent-votes' => [
                    'page'    => 1,
                    'perPage' => 1, //WalletVoterTable::defaultPerPage(),
                ],
            ];
        }
    }

    public function render(): View
    {
        return view('livewire.delegates.tabs');
    }

    // public function showWalletView(string $view): void
    // {
    //     $this->view = $view;
    // }

    public function triggerViewIsReady(?string $view = null): void
    {
        if ($view === null) {
            $view = $this->view;
        }

        if (! array_key_exists($view, $this->alreadyLoadedViews)) {
            return;
        }

        if ($this->alreadyLoadedViews[$view] === true) {
            return;
        }

        $this->emit('set'.Str::studly($view).'Ready');

        $this->alreadyLoadedViews[$view] = true;
    }

    public function updatingView(string $newView): void
    {
        if ($newView === $this->view) {
            return;
        }

        $this->previousView = $this->view;

        SupportBrowserHistoryWrapper::init()->mergeRequestQueryStringWithComponent($this);

        $this->savedQueryData[$this->view] = $this->tabQueryData[$this->view];

        // Reset the querystring data on view change to clear the URL
        $queryStringData = $this->queryString();
        foreach ($this->tabQueryData[$this->view] as $key => $value) {
            // @phpstan-ignore-next-line
            $this->{$key} = $queryStringData[$key]['except'];
        }

        $this->triggerViewIsReady($newView);
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
}
