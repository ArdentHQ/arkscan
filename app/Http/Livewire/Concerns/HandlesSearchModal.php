<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use App\Contracts\Search;
use App\Services\Forms;
use App\Services\Search\BlockSearch;
use App\Services\Search\TransactionSearch;
use App\Services\Search\WalletSearch;
use Closure;
use Illuminate\Support\Arr;
use Illuminate\View\View;

trait HandlesSearchModal
{
    public bool $isAdvanced = false;

    public bool $isRedirecting = false;

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

        $this->isRedirecting = true;

        try {
            if (! $this->isAdvanced) {
                if ($this->searchWallet($data)) {
                    return;
                } elseif ($this->searchTransaction($data)) {
                    return;
                } elseif ($this->searchBlock($data)) {
                    return;
                }
            }
        } catch (\Exception $e) {
            $this->isRedirecting = false;

            return;
        }

        $this->emitSelf('redirectToPage', 'search', [
            'state'    => $data,
            'advanced' => $this->isAdvanced ? 'true' : 'false',
        ]);
    }

    public function redirectToPage(string $route, mixed $data): void
    {
        $this->redirectRoute($route, $data);
    }

    // @phpstan-ignore-next-line - for testing exception handling
    protected function searchWallet(array $data): bool
    {
        return $this->searchWithService(new WalletSearch(), $data, fn ($model) => $this->emitSelf('redirectToPage', 'wallet', $model->address));
    }

    private function searchTransaction(array $data): bool
    {
        return $this->searchWithService(new TransactionSearch(), $data, fn ($model) => $this->emitSelf('redirectToPage', 'transaction', $model->id));
    }

    private function searchBlock(array $data): bool
    {
        return $this->searchWithService(new BlockSearch(), $data, fn ($model) => $this->emitSelf('redirectToPage', 'block', $model->id));
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
