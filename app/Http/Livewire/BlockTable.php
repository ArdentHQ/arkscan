<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Block;
use App\Services\Search\BlockSearch;
use App\ViewModels\ViewModelFactory;
use ARKEcosystem\UserInterface\Http\Livewire\Concerns\HasPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Component;

final class BlockTable extends Component
{
    use HasPagination;

    public bool $viewMore = false;

    protected ?LengthAwarePaginator $blocks = null;

    protected $listeners = ['searchBlocks'];

    public function mount(bool $viewMore = false): void
    {
        $this->viewMore = $viewMore;
    }

    public function searchBlocks(array $data): void
    {
        $this->blocks = (new BlockSearch())->search($data)->paginate();
    }

    public function render(): View
    {
        if (is_null($this->blocks)) {
            $this->blocks = Block::latestByHeight()->paginate();
        }

        return view('livewire.block-table', [
            'blocks' => ViewModelFactory::paginate($this->blocks),
        ]);
    }
}
