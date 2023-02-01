<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Facades\Network;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class MigrationController
{
    public function __invoke(): View|RedirectResponse
    {
        if (Network::hasMigration()) {
            return view('app.migration');
        }

        return redirect('/404');
    }
}
