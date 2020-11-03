<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Http\Livewire\Concerns\ManagesSearch;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
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

        if (array_key_exists('term', $data)) {
            if ($this->searchWallet($data)) {
                return;
            }

            if ($this->searchTransaction($data)) {
                return;
            }

            if ($this->searchBlock($data)) {
                return;
            }
        }

        $this->redirectRoute('search', ['state' => $data]);
    }

    private function searchWallet(array $data): bool
    {
        /** @var Wallet|null */
        $wallet = (new WalletSearch())->search(['term' => $data['term']])->first();

        if (is_null($wallet)) {
            return false;
        }

        $this->redirectRoute('wallet', $wallet->address);

        return true;
    }

    private function searchTransaction(array $data): bool
    {
        /** @var Transaction|null */
        $transaction = (new TransactionSearch())->search(['term' => $data['term']])->first();

        if (is_null($transaction)) {
            return false;
        }

        $this->redirectRoute('transaction', $transaction->id);

        return true;
    }

    private function searchBlock(array $data): bool
    {
        /** @var Block|null */
        $block = (new BlockSearch())->search(['term' => $data['term']])->first();

        if (is_null($block)) {
            return false;
        }

        $this->redirectRoute('block', $block->id);

        return true;
    }
}
