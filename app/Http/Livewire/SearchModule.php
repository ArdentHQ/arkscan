<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Contracts\Search;
use App\Http\Livewire\Concerns\ManagesSearch;
use App\Services\Forms;
use App\Services\Search\BlockSearch;
use App\Services\Search\TransactionSearch;
use App\Services\Search\WalletSearch;
use ARKEcosystem\UserInterface\Http\Livewire\Concerns\HasModal;
use Closure;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Livewire\Component;

final class SearchModule extends Component
{
    use ManagesSearch;
    use HasModal;

    public bool $isModal = false;

    public string $type = 'block';

    /** @phpstan-ignore-next-line */
    protected $queryString = [
        'state' => ['except' => []],
    ];

    /* @phpstan-ignore-next-line */
    protected $listeners = [
        'openSearchModal' => 'openModal',
    ];

    public function mount(bool $isModal = false, string $type = 'block'): void
    {
        $this->isModal    = $isModal;
        $this->type       = $type;
    }

    public function render(): View
    {
        if ($this->isModal) {
            return view('components.general.search.search-modal', [
                'transactionOptions' => Forms::getTransactionOptions(),
            ]);
        }

        return view('components.general.search.search', [
            'transactionOptions' => Forms::getTransactionOptions(),
        ]);
    }

    public function performSearch(): void
    {
        $data = $this->validateSearchQuery();

        if (array_key_exists('term', $data) && ! is_null($data['term'])) {
            $data['term'] = preg_replace('/(0x[0-9A-Z]+)/', '', $data['term']);
        }

        if ($this->searchBlock($data)) {
            return;
        }

        if ($this->searchTransaction($data)) {
            return;
        }

        if ($this->searchWallet($data)) {
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

    private function searchWithService(Search $service, array $data, Closure $callback): bool
    {
        $term = Arr::get($data, 'term');

        // Skip and search for everything if the term is empty
        if (is_null($term) || $term === '') {
            return false;
        }

        // We have an advanced search so we skip looking for a specific model
        if (count($data) > 2) {
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
