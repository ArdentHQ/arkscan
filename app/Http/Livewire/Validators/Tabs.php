<?php

declare(strict_types=1);

namespace App\Http\Livewire\Validators;

use App\Http\Livewire\Concerns\HasTabs;
use App\Http\Livewire\Concerns\SyncsInput;
use Illuminate\Contracts\View\View;
use Livewire\Component;

/**
 * @property int $page
 * @property ?int $perPage
 */
final class Tabs extends Component
{
    use HasTabs;
    use SyncsInput;

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

        // TODO: Handle filters - https://app.clickup.com/t/86dvxzge7 - see WalletTables

        return [
            'view'    => ['except' => 'validators'],
            'page'    => ['except' => 1],
            'perPage' => ['except' => $perPage],
        ];
    }

    public function mount(): void
    {
        if ($this->tabQueryData === []) {
            $this->tabQueryData = [
                'validators' => [
                    'page'    => 1,
                    'perPage' => Validators::defaultPerPage(),

                    // TODO: Filters - https://app.clickup.com/t/86dvxzge7 - see WalletTables
                ],

                'missed-blocks' => [
                    'page'    => 1,
                    'perPage' => MissedBlocks::defaultPerPage(),
                ],

                'recent-votes' => [
                    'page'    => 1,
                    'perPage' => RecentVotes::defaultPerPage(),

                    // TODO: Filters - https://app.clickup.com/t/86dvxzge7 - see WalletTables
                ],
            ];
        }
    }

    public function render(): View
    {
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
