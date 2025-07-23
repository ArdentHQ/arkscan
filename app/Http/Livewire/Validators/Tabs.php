<?php

declare(strict_types=1);

namespace App\Http\Livewire\Validators;

use App\Http\Livewire\Abstracts\TabbedComponent;
use App\Http\Livewire\Validators\Concerns\MissedBlocksTab;
use App\Http\Livewire\Validators\Concerns\RecentVotesTab;
use App\Http\Livewire\Validators\Concerns\ValidatorsTab;
use App\ViewModels\ViewModelFactory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;

final class Tabs extends TabbedComponent
{
    use MissedBlocksTab;
    use RecentVotesTab;
    use ValidatorsTab;

    public const HAS_TABLE_SORTING = true;

    public const INITIAL_VIEW = 'validators';

    public const INITIAL_FILTERS = [
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

    public string $view = 'validators';

    public ?string $previousView = 'validators';

    public array $alreadyLoadedViews = [
        'validators'    => false,
        'missed-blocks' => false,
        'recent-votes'  => false,
    ];

    public function mount(): void
    {
        parent::mount();

        if ($this->tabQueryData === []) {
            $this->tabQueryData = [
                'validators' => [
                    'paginators.validators'        => $this->paginators['validators'],
                    'paginatorsPerPage.validators' => $this->paginatorsPerPage['validators'],
                    'sortKeys.validators'          => static::defaultSortKey('VALIDATORS'),
                    'sortDirections.validators'    => static::defaultSortDirection('VALIDATORS')->value,

                    'filters.validators.active'   => $this->filters['validators']['active'],
                    'filters.validators.standby'  => $this->filters['validators']['standby'],
                    'filters.validators.resigned' => $this->filters['validators']['resigned'],
                ],

                'missed-blocks' => [
                    'paginators.missed-blocks'        => $this->paginators['missed-blocks'],
                    'paginatorsPerPage.missed-blocks' => $this->paginatorsPerPage['missed-blocks'],
                    'sortKeys.missed-blocks'          => static::defaultSortKey('MISSED_BLOCKS'),
                    'sortDirections.missed-blocks'    => static::defaultSortDirection('MISSED_BLOCKS')->value,
                ],

                'recent-votes' => [
                    'paginators.recent-votes'        => $this->paginators['recent-votes'],
                    'paginatorsPerPage.recent-votes' => $this->paginatorsPerPage['recent-votes'],
                    'sortKeys.recent-votes'          => static::defaultSortKey('RECENT_VOTES'),
                    'sortDirections.recent-votes'    => static::defaultSortDirection('RECENT_VOTES')->value,

                    'filters.recent-votes.vote'   => $this->filters['recent-votes']['vote'],
                    'filters.recent-votes.unvote' => $this->filters['recent-votes']['unvote'],
                ],
            ];
        }
    }

    public function render(): View
    {
        return view('livewire.validators.tabs', [
            'validators'   => ViewModelFactory::paginate($this->validators),
            'missedBlocks' => ViewModelFactory::paginate($this->missedBlocks),
            'recentVotes'  => ViewModelFactory::paginate($this->recentVotes),
        ]);
    }

    #[On('showValidatorsView')]
    public function showValidatorsView(string $view): void
    {
        $this->syncInput('view', $view);
    }
}
