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

    public bool $isAdvanced = false;

    public string $type = 'block';

    /** @phpstan-ignore-next-line */
    protected $queryString = [
        'state' => ['except' => []],
    ];

    protected array $transactionOptionsValues = [
        '' => [
            'all',
        ],
        'core' => [
            'transfer',
            'secondSignature',
            'delegateRegistration',
            'vote',
            'voteCombination',
            'multiSignature',
            'ipfs',
            'multiPayment',
            'timelock',
            'timelockClaim',
            'timelockRefund',
        ],
        'magistrate' => [
            'businessEntityRegistration',
            'businessEntityResignation',
            'businessEntityUpdate',
            'delegateEntityRegistration',
            'delegateEntityResignation',
            'delegateEntityUpdate',
            'delegateResignation',
            'entityRegistration',
            'entityResignation',
            'entityUpdate',
            'legacyBridgechainRegistration',
            'legacyBridgechainResignation',
            'legacyBridgechainUpdate',
            'legacyBusinessRegistration',
            'legacyBusinessResignation',
            'legacyBusinessUpdate',
            'moduleEntityRegistration',
            'moduleEntityResignation',
            'moduleEntityUpdate',
            'pluginEntityRegistration',
            'pluginEntityResignation',
            'pluginEntityUpdate',
            'productEntityRegistration',
            'productEntityResignation',
            'productEntityUpdate',
        ],
    ];

    public function mount(bool $isSlim = false, bool $isAdvanced = false, string $type = 'block'): void
    {
        $this->isAdvanced = $isAdvanced;
        $this->isSlim     = $isSlim;
        $this->type       = $type;
    }

    public function render(): View
    {
        return view('components.search', [
            'transactionOptions' => $this->getTransactionOptions(),
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

    /**
     * Map the transaction options as the rich select component expects.
     */
    private function getTransactionOptions(): array
    {
        return collect($this->transactionOptionsValues)
            ->mapWithKeys(function ($options, $group): array {
                $key = strtoupper($group);
                $value = collect($options)
                    ->mapWithKeys(function ($option): array {
                        return [$option => __('forms.search.transaction_types.'.$option)];
                    })->toArray();

                return [
                    $key => $value,
                ];
            })->toArray();
    }
}
