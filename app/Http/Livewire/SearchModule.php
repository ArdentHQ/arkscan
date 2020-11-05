<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Contracts\Search;
use App\Http\Livewire\Concerns\ManagesSearch;
use App\Services\Search\BlockSearch;
use App\Services\Search\TransactionSearch;
use App\Services\Search\WalletSearch;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Livewire\Component;

final class SearchModule extends Component
{
    use ManagesSearch;

    public bool $isSlim = false;

    /** @phpstan-ignore-next-line */
    protected $queryString = [
        'state' => ['except' => []],
    ];

    public function mount(bool $isSlim = false): void
    {
        $this->isSlim = $isSlim;
    }

    public function render(): View
    {
        return view('components.search', [
            'isAdvanced' => false,
            'type'       => Arr::get($this->state, 'type', 'block'),
        ]);
    }

    public function performSearch(): void
    {
        $data = $this->validateSearchQuery();

        if ($this->searchWallet($data)) {
            return;
        }

        if ($this->searchTransaction($data)) {
            return;
        }

        if ($this->searchBlock($data)) {
            return;
        }

        $this->redirectRoute('search', ['state' => $data]);
    }

    private function searchWallet(array $data): bool
    {
        return $this->searchWithService(new WalletSearch(), $data, fn ($model) => $this->redirectRoute('wallet', $model->address));
    }

    private function searchTransaction(array $data): bool
    {
        return $this->searchWithService(new TransactionSearch(), $data, fn ($model) => $this->redirectRoute('transaction', $model->id));
    }

    private function searchBlock(array $data): bool
    {
        return $this->searchWithService(new BlockSearch(), $data, fn ($model) => $this->redirectRoute('block', $model->id));
    }

    private function searchWithService(Search $service, array $data, \Closure $callback): bool
    {
        $term = Arr::get($data, 'term');

        if (is_null($term) || $term === '') {
            return false;
        }

        $model = $service->search(['term' => $term])->first();

        if (is_null($model)) {
            return false;
        }

        $callback($model);

        return true;
    }
}
