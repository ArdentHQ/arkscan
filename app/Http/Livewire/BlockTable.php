<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Block;
use App\Models\Scopes\OrderByTimestampScope;
use App\ViewModels\ViewModelFactory;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Component;

final class BlockTable extends Component
{
    use HasPagination;

    public const PER_PAGE = 15;

    /** @var mixed */
    protected $listeners = ['currencyChanged' => '$refresh'];

    public function render(): View
    {
        /** @var Block $lastBlock */
        $lastBlock = Block::withScope(OrderByTimestampScope::class)->first();

        $lastBlockHeight = $lastBlock->height->toNumber();
        $heightTo        = $lastBlockHeight - (self::PER_PAGE * ($this->page - 1));
        $heightFrom      = $heightTo - self::PER_PAGE;

        $blocks = Block::withScope(OrderByTimestampScope::class)
            ->where('height', '<=', $heightTo)
            ->where('height', '>', $heightFrom)
            ->get();

        $blocks = new LengthAwarePaginator($blocks, $lastBlockHeight, self::PER_PAGE, $this->page, [
            'path'     => route('blocks'),
            'pageName' => 'page',
        ]);

        return view('livewire.block-table', [
            'blocks' => ViewModelFactory::paginate($blocks),
        ]);
    }
}
