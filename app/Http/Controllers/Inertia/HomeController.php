<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use App\Facades\Network;
use Inertia\Inertia;
use Inertia\Response;

final class HomeController
{
    public function __invoke(): Response
    {
        return Inertia::render('Home/Index', [])
            ->withMeta('home', [
                'name' => Network::currency(),
            ]);
    }
}
