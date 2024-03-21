<?php

declare(strict_types=1);

namespace App\Http\Livewire\Validators;

use App\Http\Livewire\Concerns\HasTabs;
use Illuminate\Contracts\View\View;
use Livewire\Component;

/**
 * @property int $page
 * @property int $perPage
 */
final class Tabs extends Component
{
    use HasTabs;

    public string $view = 'validator';

    public ?string $previousView = 'validator';

    public array $tabQueryData = [];

    public array $savedQueryData = [];

    public array $alreadyLoadedViews = [
        'validator'     => false,
        'missed-blocks' => false,
        'recent-votes'  => false,
    ];

    /** @var mixed */
    protected $listeners = [
        'showValidatorsView',
    ];

    public function queryString(): array
    {
        $perPage = intval(config('arkscan.pagination.per_page'));
        if ($this->view === 'validator') {
            $perPage = Validators::defaultPerPage();
        }

        // TODO: Handle filters - https://app.clickup.com/t/861n4ydmh - see WalletTables

        return [
            'view'    => ['except' => 'validator'],
            'page'    => ['except' => 1],
            'perPage' => ['except' => $perPage],
        ];
    }

    public function boot(): void
    {
        if ($this->tabQueryData === []) {
            $this->tabQueryData = [
                'validator' => [
                    'page'    => 1,
                    'perPage' => Validators::defaultPerPage(),

                    // TODO: Filters - https://app.clickup.com/t/861n4ydmh - see WalletTables
                ],

                'missed-blocks' => [
                    'page'    => 1,
                    'perPage' => MissedBlocks::defaultPerPage(),
                ],

                'recent-votes' => [
                    'page'    => 1,
                    'perPage' => RecentVotes::defaultPerPage(),

                    // TODO: Filters - https://app.clickup.com/t/861n4ydmh - see WalletTables
                ],
            ];
        }
    }

    public function render(): View
    {
        return view('livewire.validator.tabs');
    }

    public function showValidatorsView(string $view): void
    {
        $this->syncInput('view', $view);
    }

    private function tabbedComponent(): string
    {
        return [
            'validator'     => Validators::class,
            'missed-blocks' => MissedBlocks::class,
            'recent-votes'  => RecentVotes::class,
        ][$this->view];
    }
}
