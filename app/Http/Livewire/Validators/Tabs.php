<?php

declare(strict_types=1);

namespace App\Http\Livewire\Validators;

use App\Http\Livewire\Concerns\HasTabs;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
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

    public array $tabQueryData = [];

    public array $savedQueryData = [];

    public array $alreadyLoadedViews = [
        'validators'     => false,
        'missed-blocks'  => false,
        'recent-votes'   => false,
    ];

    /** @var mixed */
    protected $listeners = [
        'showValidatorsView',
    ];

    public function queryString(): array
    {
        $perPage = intval(config('arkscan.pagination.per_page'));
        if ($this->view === 'validators') {
            $perPage = Validators::defaultPerPage();
        }

        Log::debug('Validators Tabs Query String', [
            'view'    => $this->view,
            'perPage' => $perPage,
        ]);

        // TODO: Handle filters - https://app.clickup.com/t/86dvxzge7 - see WalletTables

        return [
            'view'               => ['except' => 'validators'],
            'paginators.page'    => ['except' => 1, 'history' => true],
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
                    'paginators.page'    => 1,
                    'perPage'            => Validators::defaultPerPage(),
                    'sortKey'            => Validators::defaultSortKey(),
                    'sortDirection'      => Validators::defaultSortDirection(),

                    // TODO: Filters - https://app.clickup.com/t/86dvxzge7 - see WalletTables
                ],

                'missed-blocks' => [
                    'paginators.page' => 1,
                    'perPage'         => MissedBlocks::defaultPerPage(),
                    'sortKey'         => MissedBlocks::defaultSortKey(),
                    'sortDirection'   => MissedBlocks::defaultSortDirection(),
                ],

                'recent-votes' => [
                    'paginators.page' => 1,
                    'perPage'         => RecentVotes::defaultPerPage(),
                    'sortKey'         => RecentVotes::defaultSortKey(),
                    'sortDirection'   => RecentVotes::defaultSortDirection(),

                    // TODO: Filters - https://app.clickup.com/t/86dvxzge7 - see WalletTables
                ],
            ];
        }
    }

    public function render(): View
    {
        Log::debug('render', [
            'perPage' => $this->perPage,
        ]);

        return view('livewire.validators.tabs');
    }

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
