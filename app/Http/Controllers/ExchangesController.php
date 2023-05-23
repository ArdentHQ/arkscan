<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Facades\Network;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class ExchangesController
{
    public function __invoke(): View|RedirectResponse
    {
        if (! Network::canBeExchanged()) {
            return redirect()->route('home');
        }

        return view('app.exchanges');
    }
}
