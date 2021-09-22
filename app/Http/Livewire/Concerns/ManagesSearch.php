<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use Illuminate\Validation\Rule;

trait ManagesSearch
{
    public array $state = [];

    private function validateSearchQuery(): array
    {
        return $this->validate([
            // Generic
            'state'             => 'array',
            'state.term'        => ['nullable', 'string', 'max:66'],
            'state.type'        => ['nullable', Rule::in(['block', 'transaction', 'wallet'])],
            'state.dateFrom'    => ['nullable', 'date'],
            'state.dateTo'      => ['nullable', 'date'],
            // Blocks
            'state.heightFrom'         => ['nullable', 'numeric', 'min:1'],
            'state.heightTo'           => ['nullable', 'numeric', 'min:1'],
            'state.totalAmountFrom'    => ['nullable', 'numeric', 'min:0'],
            'state.totalAmountTo'      => ['nullable', 'numeric', 'min:0'],
            'state.totalFeeFrom'       => ['nullable', 'numeric', 'min:0'],
            'state.totalFeeTo'         => ['nullable', 'numeric', 'min:0'],
            'state.rewardFrom'         => ['nullable', 'numeric', 'min:0'],
            'state.rewardTo'           => ['nullable', 'numeric', 'min:0'],
            'state.generatorPublicKey' => ['nullable', 'string', 'max:66'],
            // Transactions
            'state.transactionType' => ['nullable', Rule::in(array_keys(trans('forms.search.transaction_types')))],
            'state.amountFrom'      => ['nullable', 'numeric', 'min:0'],
            'state.amountTo'        => ['nullable', 'numeric', 'min:0'],
            'state.feeFrom'         => ['nullable', 'numeric', 'min:0'],
            'state.feeTo'           => ['nullable', 'numeric', 'min:0'],
            'state.smartBridge'     => ['nullable', 'string', 'max:255'],
            // Wallets
            'state.username'    => ['nullable', 'string', 'min:1', 'max:20'],
            'state.vote'        => ['nullable', 'string', 'max:66'],
            'state.balanceFrom' => ['nullable', 'numeric', 'min:0'],
            'state.balanceTo'   => ['nullable', 'numeric', 'min:0'],
        ])['state'];
    }
}
