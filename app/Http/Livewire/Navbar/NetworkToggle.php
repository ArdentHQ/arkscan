<?php

declare(strict_types=1);

namespace App\Http\Livewire\Navbar;

use App\Facades\Network;
use Illuminate\Http\RedirectResponse;

final class NetworkToggle extends Toggle
{
    /**
     * @return void|RedirectResponse
     */
    public function toggle()
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
