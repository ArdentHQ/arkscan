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
            'state.term'        => ['nullable', 'string', 'max:255'],
            'state.type'        => ['nullable', Rule::in(['block', 'transaction', 'wallet'])],
            'state.dateFrom'    => ['nullable', 'date'],
            'state.dateTo'      => ['nullable', 'date'],
            // Blocks
            'state.heightFrom'         => ['nullable', 'integer', 'min:1'],
            'state.heightTo'           => ['nullable', 'integer', 'min:1'],
            'state.totalAmountFrom'    => ['nullable', 'integer', 'min:0'],
            'state.totalAmountTo'      => ['nullable', 'integer', 'min:0'],
            'state.totalFeeFrom'       => ['nullable', 'integer', 'min:0'],
            'state.totalFeeTo'         => ['nullable', 'integer', 'min:0'],
            'state.generatorPublicKey' => ['nullable', 'string', 'max:255'],
            // Transactions
            'state.transactionType' => ['nullable', 'string'], // @TODO: validate based on an enum
            'state.amountFrom'      => ['nullable', 'integer', 'min:0', 'max:100'],
            'state.amountTo'        => ['nullable', 'integer', 'min:0', 'max:100'],
            'state.feeFrom'         => ['nullable', 'integer', 'min:0', 'max:100'],
            'state.feeTo'           => ['nullable', 'integer', 'min:0', 'max:100'],
            'state.smartBridge'     => ['nullable', 'string', 'max:255'],
            // Wallets
            'state.username'    => ['nullable', 'string', 'max:255'],
            'state.vote'        => ['nullable', 'string', 'max:255'],
            'state.balanceFrom' => ['nullable', 'integer', 'min:0', 'max:100'],
            'state.balanceTo'   => ['nullable', 'integer', 'min:0', 'max:100'],
        ])['state'];
    }
}
