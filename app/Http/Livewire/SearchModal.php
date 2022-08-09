<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Contracts\Search;
use App\Http\Livewire\Concerns\ManagesSearch;
use App\Services\Forms;
use App\Services\Search\BlockSearch;
use App\Services\Search\TransactionSearch;
use App\Services\Search\WalletSearch;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasModal;
use Closure;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Livewire\Component;

final class SearchModal extends Component
{
    use ManagesSearch;
    use HasModal;

    public bool $isAdvanced = false;

    /** @var mixed */
    protected $listeners = [
        'openSearchModal' => 'openModal',
    ];

    public function mount(string $type = 'block'): void
    {
        $this->state['type'] = $type;
    }

    public function render(): View
    {
        return view('components.general.search.search-modal', [
            'transactionOptions' => Forms::getTransactionOptions(),
            'type'               => $this->state['type'],
        ]);
    }

    public function performSearch(): void
    {
        $data = $this->validateSearchQuery();

        if (array_key_exists('term', $data) && ! is_null($data['term'])) {
            $data['term'] = preg_replace('/(0x[0-9A-Z]+)/', '', $data['term']);
        }

        if ($this->isAdvanced) {
            if ($data['type'] === 'wallet' && $this->searchWallet($data)) {
                return;
            } elseif ($data['type'] === 'block' && $this->searchBlock($data)) {
                return;
            } elseif ($data['type'] === 'transaction' && $this->searchTransaction($data, true)) {
                return;
            }
        } else {
            if ($this->searchWallet($data)) {
                return;
            } elseif ($this->searchTransaction($data)) {
                return;
            } elseif ($this->searchBlock($data)) {
                return;
            }
        }

        $this->redirectRoute('search', [
            'state'    => $data,
            'advanced' => $this->isAdvanced ? 'true' : 'false',
        ]);
    }

    private function searchWallet(array $data): bool
    {
        return $this->searchWithService(new WalletSearch(), $data, fn ($model) => $this->redirectRoute('wallet', $model->address));
    }

    private function searchTransaction(array $data, bool $searchBlocks = false): bool
    {
        return $this->searchWithService(new TransactionSearch($searchBlocks), $data, fn ($model) => $this->redirectRoute('transaction', $model->id));
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

        $model = $service->search(['term' => $term])->first();

        if (is_null($model)) {
            return false;
        }

        $callback($model);

        return true;
    }
}
