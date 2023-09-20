<?php

declare(strict_types=1);

namespace App\Http\Livewire\Home;

use App\Http\Livewire\Concerns\ManagesLatestBlocks;
use App\ViewModels\ViewModelFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

final class Blocks extends Component
{
    use ManagesLatestBlocks;

    public array $state = [
        'selected' => 'blocks',
        'type'     => 'all',
    ];

    /** @phpstan-ignore-next-line */
    protected $listeners = ['currencyChanged'];

    private ?Collection $blocks = null;

    public function render(): View
    {
        return $this->renderBlocks();
    }

    public function currencyChanged(): void
    {
        $this->pollBlocks();
    }

    private function renderBlocks(): View
    {
        if (is_null($this->blocks)) {
            $this->blocks = new Collection();
        }

        $this->state['type'] = 'all';

        return view('livewire.home.blocks', [
            'blocks' => ViewModelFactory::collection($this->blocks),
        ]);
    }
}
