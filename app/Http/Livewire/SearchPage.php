<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Http\Livewire\Concerns\ManagesSearch;
use App\Services\Search\BlockSearch;
use App\Services\Search\TransactionSearch;
use App\Services\Search\WalletSearch;
use App\ViewModels\ViewModelFactory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Livewire\Component;
use Livewire\WithPagination;

final class SearchPage extends Component
{
    use ManagesSearch;
    use WithPagination;

    public bool $isAdvanced = true;

    /** @var mixed */
    protected $listeners = ['pageChanged' => 'performSearch'];

    /** @var mixed */
    protected $queryString = [
        'state',
        'isAdvanced' => [
            'except' => false,
            'as'     => 'advanced',
        ],
    ];

    private ?LengthAwarePaginator $results = null;

    public function mount(): void
    {
        $this->restoreState(request('state', []));

        if ($this->isAdvanced) {
            $this->performSearch();
        }
    }

    public function render(): View
    {
        return view('livewire.search-page', [
            'results' => $this->results,
        ]);
    }

    public function performSearch(): void
    {
        $data = $this->validateSearchQuery();

        if ($this->isAdvanced) {
            if ($data['type'] === 'wallet') {
                $this->results = (new WalletSearch())->search($data)->paginate();
            } elseif ($data['type'] === 'block') {
                $this->results = (new BlockSearch())->search($data)->paginate();
            } elseif ($data['type'] === 'transaction') {
                $this->results = (new TransactionSearch(true))->search($data)->paginate();
            }
        } else {
            $this->state['type'] = 'wallet';

            $this->results = (new WalletSearch())->search(['term' => Arr::get($data, 'term')])->paginate();

            if ($this->results->isEmpty()) {
                $this->state['type'] = 'transaction';

                $this->results = (new TransactionSearch())->search(['term' => Arr::get($data, 'term')])->paginate();
            }

            if ($this->results->isEmpty()) {
                $this->state['type'] = 'block';

                $this->results = (new BlockSearch())->search(['term' => Arr::get($data, 'term')])->paginate();
            }
        }

        if ($this->results !== null) {
            $this->results = ViewModelFactory::paginate($this->results);
        }
    }

    // @codeCoverageIgnoreStart
    public function gotoPage(int $page): void
    {
        $this->emit('pageChanged');
        $this->setPage($page);
        $this->performSearch();
    }

    // @codeCoverageIgnoreEnd

    private function restoreState(array $state): void
    {
        $this->state = array_merge([
            // Generic
            'term'        => null,
            'type'        => 'block',
            'dateFrom'    => null,
            'dateTo'      => null,
            // Blocks
            'totalAmountFrom'    => null,
            'totalAmountTo'      => null,
            'totalFeeFrom'       => null,
            'totalFeeTo'         => null,
            'rewardFrom'         => null,
            'rewardTo'           => null,
            'generatorPublicKey' => null,
            // Transactions
            'transactionType' => 'all',
            'amountFrom'      => null,
            'amountTo'        => null,
            'feeFrom'         => null,
            'feeTo'           => null,
            'smartBridge'     => null,
            // Wallets
            'username'    => null,
            'vote'        => null,
            'balanceFrom' => null,
            'balanceTo'   => null,
        ], $state);
    }
}
