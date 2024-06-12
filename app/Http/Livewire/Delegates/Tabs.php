<?php

declare(strict_types=1);

namespace App\Http\Livewire\Delegates;

use App\Http\Livewire\Concerns\HasTabs;
use App\Http\Livewire\Concerns\SyncsInput;
use Illuminate\Contracts\View\View;
use Livewire\Component;

/**
 * @property int $page
 * @property int $perPage
 */
final class Tabs extends Component
{
    use HasTabs;
    use SyncsInput;

    public string $view = 'delegates';

    public ?string $previousView = 'delegates';

    public array $tabQueryData = [];

    public array $savedQueryData = [];

    public array $alreadyLoadedViews = [
        'delegates'     => false,
        'missed-blocks' => false,
        'recent-votes'  => false,
    ];

    /** @var mixed */
    protected $listeners = [
        'showDelegatesView',
    ];

    public function queryString(): array
    {
        $perPage = intval(config('arkscan.pagination.per_page'));
        if ($this->view === 'delegates') {
            $perPage = Delegates::defaultPerPage();
        }

        // TODO: Handle filters - https://app.clickup.com/t/861n4ydmh - see WalletTables

        return [
            'view'    => ['except' => 'delegates'],
            'page'    => ['except' => 1],
            'perPage' => ['except' => $perPage],
        ];
    }

    public function mount(): void
    {
        if ($this->tabQueryData === []) {
            $this->tabQueryData = [
                'delegates' => [
                    'page'    => 1,
                    'perPage' => Delegates::defaultPerPage(),

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
        return view('livewire.delegates.tabs');
    }

    public function showDelegatesView(string $view): void
    {
        $this->syncInput('view', $view);
    }

    private function tabbedComponent(): string
    {
        return [
            'delegates'     => Delegates::class,
            'missed-blocks' => MissedBlocks::class,
            'recent-votes'  => RecentVotes::class,
        ][$this->view];
    }
}
