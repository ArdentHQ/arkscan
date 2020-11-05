<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Network;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Component;

final class DelegateTabs extends Component
{
    public function render(): View
    {
        return view('livewire.delegate-tabs', [
            'countActive'   => $this->activeQuery()->count(),
            'countStandby'  => $this->standbyQuery()->count(),
            'countResigned' => $this->resignedQuery()->count(),
        ]);
    }

    public function activeQuery(): Builder
    {
        return DB::connection('explorer')
            ->table('wallets')
            ->whereNotNull('attributes->delegate->username')
            ->whereRaw("(\"attributes\"->'delegate'->>'rank')::numeric <= ?", [Network::delegateCount()]);
    }

    public function standbyQuery(): Builder
    {
        return DB::connection('explorer')
            ->table('wallets')
            ->whereNotNull('attributes->delegate->username')
            ->whereRaw("(\"attributes\"->'delegate'->>'rank')::numeric > ?", [Network::delegateCount()]);
    }

    public function resignedQuery(): Builder
    {
        return DB::connection('explorer')
            ->table('wallets')
            ->whereNotNull('attributes->delegate->username')
            ->where('attributes->delegate->resigned', true);
    }
}
