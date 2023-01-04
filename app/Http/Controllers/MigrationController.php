<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;

final class MigrationController
{
    public function __invoke(): View
    {
        return view('app.migration');
    }
}
