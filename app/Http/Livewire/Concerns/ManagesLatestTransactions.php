<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Transaction;
use App\Services\Cache\TableCache;
use Illuminate\Database\Eloquent\Collection;

trait ManagesLatestTransactions
{
    public bool $isLoading = false;

    public function pollTransactions(): void
    {
        $this->transactions = (new TableCache())->setLatestTransactions($this->state['type'], function (): Collection {
            $query          = Transaction::withScope(OrderByTimestampScope::class);

            if ($this->state['type'] !== 'all') {
                $scopeClass = Transaction::TYPE_SCOPES[$this->state['type']];

                /* @var \Illuminate\Database\Eloquent\Model */
                $query = $query->withScope($scopeClass);
            }

            return $query->take(15)->get();
        });

        $this->isLoading = false;
    }

    public function updatedStateType(): void
    {
        $this->isLoading    = true;
        $this->transactions = null;

        $this->emitSelf('refreshLatestTransactions');
    }
}
