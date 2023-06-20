<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Http\Livewire\Concerns\HasTablePagination;
use App\Models\Block;
use App\Models\Scopes\OrderByHeightScope;
use App\ViewModels\ViewModelFactory;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Component;

/** @property LengthAwarePaginator $blocks */
final class WalletBlockTable extends Component
{
    use HasTablePagination;

    public const PER_PAGE = 10;

    public string $publicKey;

    public string $username;

    /** @var mixed */
    protected $listeners = ['currencyChanged' => '$refresh'];

    public function mount(string $publicKey, string $username): void
    {
        $this->publicKey = $publicKey;
        $this->username  = $username;
    }

    public function render(): View
    {
        return view('livewire.wallet-block-table', [
            'blocks' => ViewModelFactory::paginate($this->blocks),
        ]);
    }

    public function getNoResultsMessageProperty(): ?string
    {
        if ($this->blocks->total() === 0) {
            return trans('tables.blocks.no_results');
        }

        return null;
    }

    public function getBlocksProperty(): LengthAwarePaginator
    {
        return Block::where('generator_public_key', $this->publicKey)
            ->withScope(OrderByHeightScope::class)
            ->paginate($this->perPage);
    }
}
