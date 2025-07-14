<?php

declare(strict_types=1);

namespace App\Http\Livewire\Validators;

use App\Http\Livewire\Concerns\HasTabs;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * @property int $page
 * @property ?int $perPage
 */
final class Tabs extends Component
{
    use HasTabs;

    public string $view = 'validators';

    public ?string $previousView = 'validators';

    public array $alreadyLoadedViews = [
        'validators'     => false,
        'missed-blocks'  => false,
        'recent-votes'   => false,
    ];

    public function queryString(): array
    {
        $perPage = intval(config('arkscan.pagination.per_page'));
        if ($this->view === 'validators') {
            $perPage = Validators::defaultPerPage();
        }

        // TODO: Handle filters - https://app.clickup.com/t/86dvxzge7 - see WalletTables

        return [
            'view'               => ['except' => 'validators'],
            'paginators.page'    => ['except' => 1],
            // 'paginators' => [
            //     'page'    => ['except' => 1],
            // ],
            'perPage'            => ['except' => $perPage],
            'sortKey'            => ['except' => Validators::defaultSortKey()],
            'sortDirection'      => ['except' => Validators::defaultSortDirection()],
        ];
    }

    public function boot(): void
    {
    }

    public function mount(): void
    {
        if ($this->tabQueryData === []) {
            $this->tabQueryData = [
                'validators' => [
                    // 'paginators.page'    => 1,
                    'paginators' => [
                        'page'    => ['except' => 1],
                    ],
                    'perPage'            => Validators::defaultPerPage(),
                    'sortKey'            => Validators::defaultSortKey(),
                    'sortDirection'      => Validators::defaultSortDirection(),

                    // TODO: Filters - https://app.clickup.com/t/86dvxzge7 - see WalletTables
                ],

                'missed-blocks' => [
                    // 'paginators.page' => 1,
                    'paginators' => [
                        'page'    => ['except' => 1],
                    ],
                    'perPage'         => MissedBlocks::defaultPerPage(),
                    'sortKey'         => MissedBlocks::defaultSortKey(),
                    'sortDirection'   => MissedBlocks::defaultSortDirection(),
                ],

                'recent-votes' => [
                    // 'paginators.page' => 1,
                    'paginators' => [
                        'page'    => ['except' => 1],
                    ],
                    'perPage'         => RecentVotes::defaultPerPage(),
                    'sortKey'         => RecentVotes::defaultSortKey(),
                    'sortDirection'   => RecentVotes::defaultSortDirection(),

                    // TODO: Filters - https://app.clickup.com/t/86dvxzge7 - see WalletTables
                ],
            ];

            $view = $this->resolveView();
            if (! array_key_exists($view, $this->tabQueryData)) {
                return;
            }

            // $perPage = $this->resolvePerPage();
            // if ($perPage !== null) {
            //     $this->tabQueryData[$view]['perPage'] = $perPage;
            // }

            // $this->gotoPage($this->resolvePage(), false);
        }
    }

    public function render(): View
    {
        return view('livewire.validators.tabs');
    }

    #[On('showValidatorsView')]
    public function showValidatorsView(string $view): void
    {
        $this->syncInput('view', $view);
    }

    private function tabbedComponent(): string
    {
        return [
            'validators'    => Validators::class,
            'missed-blocks' => MissedBlocks::class,
            'recent-votes'  => RecentVotes::class,
        ][$this->view];
    }
}
