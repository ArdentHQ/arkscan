<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Transaction;
use App\Services\Cache\TableCache;

trait ManagesLatestTransactions
{
    public function pollTransactions(): void
    {
        $this->transactions = (new TableCache())->setLatestTransactions($this->state['type'], function () {
            $query = Transaction::withScope(OrderByTimestampScope::class);

            if ($this->state['type'] !== 'all') {
                $scopeClass = Transaction::TYPE_SCOPES[$this->state['type']];

                /* @var \Illuminate\Database\Eloquent\Model */
                $query = $query->withScope($scopeClass);
            }

            return $query->take(15)->get();
        });
    }

    public function updatedStateType(): void
    {
        $this->pollTransactions();
    }
}
