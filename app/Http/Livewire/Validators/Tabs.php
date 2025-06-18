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

    // public int $page = 1;

    public string $view = 'validators';

    public ?string $previousView = 'validators';

    public array $tabQueryData = [];

    public array $savedQueryData = [];

    public array $alreadyLoadedViews = [
        'validators'     => false,
        // 'missed-blocks'  => false,
        // 'recent-votes'   => false,
    ];

    /** @var mixed */
    protected $listeners = [
        'showValidatorsView',
    ];

    public function queryString(): array
    {
        // dump('qqq');
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
            'view'    => ['except' => 'validators', 'history' => true],
            'paginators.page'    => ['except' => 1, 'history' => true],
            'perPage' => ['except' => $perPage, 'history' => true],
        ];
    }

    public function boot(): void
    {
        // dump('bro');
    }

    public function mount(): void
    {
        // dump('www');
        if ($this->tabQueryData === []) {
            $this->tabQueryData = [
                'validators' => [
                    'paginators.page'    => 1,
                    'perPage' => Validators::defaultPerPage(),

                    // TODO: Filters - https://app.clickup.com/t/86dvxzge7 - see WalletTables
                ],

                // 'missed-blocks' => [
                //     'paginators.page'    => 1,
                //     'perPage' => MissedBlocks::defaultPerPage(),
                // ],

                // 'recent-votes' => [
                //     'paginators.page'    => 1,
                //     'perPage' => RecentVotes::defaultPerPage(),

                //     // TODO: Filters - https://app.clickup.com/t/86dvxzge7 - see WalletTables
                // ],
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
        // dump('ttt');
        return [
            'validators'    => Validators::class,
            // 'missed-blocks' => MissedBlocks::class,
            // 'recent-votes'  => RecentVotes::class,
        ][$this->view];
    }
}
