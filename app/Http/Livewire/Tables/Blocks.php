<?php

declare(strict_types=1);

namespace App\Http\Livewire\Tables;

use App\ViewModels\ViewModelFactory;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasPagination;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Component;

final class Blocks extends Component
{
    use HasPagination;

    protected LengthAwarePaginator $blocks;

    public function mount(Builder $blocks): void
    {
        $this->blocks = $blocks->paginate();
    }

    public function render(): View
    {
        return view('livewire.tables.blocks', [
            'blocks' => ViewModelFactory::paginate($this->blocks),
        ]);
    }
}
