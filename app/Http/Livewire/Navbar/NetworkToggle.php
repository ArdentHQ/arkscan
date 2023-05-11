<?php

declare(strict_types=1);

namespace App\Http\Livewire\Navbar;

use App\Facades\Network;

final class NetworkToggle extends Toggle
{
    public function toggle(): mixed
    {
        if ($this->isActive()) {
            return response()->redirectTo(Network::mainnetExplorerUrl());
        }

        return response()->redirectTo(Network::testnetExplorerUrl());
    }

    public function isActive(): bool
    {
        return config('explorer.network') !== 'production';
    }
}
