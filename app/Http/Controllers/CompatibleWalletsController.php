<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

final class CompatibleWalletsController
{
    public function __invoke(): View
    {
        return view('app.compatible-wallets');
    }
}
