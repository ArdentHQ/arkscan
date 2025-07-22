<?php

declare(strict_types=1);

namespace App\Http\Livewire\Validators;

use App\Enums\SortDirection;
use App\Http\Livewire\Concerns\HasTableFilter;
use App\Http\Livewire\Abstracts\TabbedComponent;
use App\Http\Livewire\Validators\Concerns\MissedBlocksTab;
use App\Http\Livewire\Validators\Concerns\RecentVotesTab;
use App\Http\Livewire\Validators\Concerns\ValidatorsTab;
use App\ViewModels\ViewModelFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

final class Tabs extends TabbedComponent
{
    use HasTableFilter;
    use MissedBlocksTab;
    use RecentVotesTab;
    use ValidatorsTab;

    public const INITIAL_VIEW = 'validators';

    public string $view = 'validators';

    public ?string $previousView = 'validators';

    public array $tabQueryData = [];

    public array $savedQueryData = [];

    public array $alreadyLoadedViews = [
        'validators'    => false,
        'missed-blocks' => false,
        'recent-votes'  => false,
    ];

    public function mount(): void
    {
        parent::mount();

        if (count($this->filters) === 0) {
            $this->filters = [
                'validators' => [
                    'active'   => true,
                    'standby'  => true,
                    'resigned' => false,
                ],
                'recent-votes' => [
                    'vote'   => true,
                    'unvote' => true,
                ],
            ];
        }

        if ($this->tabQueryData === []) {
            $this->tabQueryData = [
                'validators' => [
                    'paginators.validators' => $this->paginators['validators'],
                    'paginatorsPerPage.validators' => $this->paginatorsPerPage['validators'],
                    'sortKeys.validators' => static::defaultSortKey('VALIDATORS'),
                    'sortDirections.validators' => static::defaultSortDirection('VALIDATORS')->value,

                    // TODO: Filters - https://app.clickup.com/t/86dvxzge7 - see WalletTables
                ],

                'missed-blocks' => [
                    'paginators.missed-blocks'    => $this->paginators['missed-blocks'],
                    'paginatorsPerPage.missed-blocks' => $this->paginatorsPerPage['missed-blocks'],
                    'sortKeys.missed-blocks' => static::defaultSortKey('MISSED_BLOCKS'),
                    'sortDirections.missed-blocks' => static::defaultSortDirection('MISSED_BLOCKS')->value,
                ],

                'recent-votes' => [
                    'paginators.recent-votes'    => $this->paginators['recent-votes'],
                    'paginatorsPerPage.recent-votes' => $this->paginatorsPerPage['recent-votes'],
                    'sortKeys.recent-votes' => static::defaultSortKey('RECENT_VOTES'),
                    'sortDirections.recent-votes' => static::defaultSortDirection('RECENT_VOTES')->value,

                    // TODO: Filters - https://app.clickup.com/t/86dvxzge7 - see WalletTables
                ],
            ];
        }
    }

    public function render(): View
    {
        return view('livewire.validators.tabs', [
            'validators' => ViewModelFactory::paginate($this->validators),
            'missedBlocks' => ViewModelFactory::paginate($this->missedBlocks),
            'recentVotes' => ViewModelFactory::paginate($this->recentVotes),
        ]);
    }

    #[On('showValidatorsView')]
    public function showValidatorsView(string $view): void
    {
        $this->syncInput('view', $view);
    }
}
