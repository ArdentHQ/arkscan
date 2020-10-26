<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use App\Facades\Network;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Builder;

trait HasDelegateQueries
{
    public function activeQuery(): Builder
    {
        return Wallet::query()
            ->whereNotNull('attributes->delegate->username')
            ->where('attributes->delegate->rank', '<=', Network::delegateCount())
            ->orderBy('attributes->delegate->rank', 'asc')
            ->limit(Network::delegateCount());
    }

    public function standbyQuery(): Builder
    {
        return Wallet::query()
            ->whereNotNull('attributes->delegate->username')
            ->where('attributes->delegate->rank', '>', Network::delegateCount())
            ->orderBy('attributes->delegate->rank', 'asc');
    }

    public function resignedQuery(): Builder
    {
        return Wallet::query()
            ->whereNotNull('attributes->delegate->username')
            ->where('attributes->delegate->resigned', true);
    }
}
