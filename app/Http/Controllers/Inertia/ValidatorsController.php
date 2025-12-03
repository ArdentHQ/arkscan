<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use Inertia\Inertia;
use Inertia\Response;

final class ValidatorsController
{
    public function __invoke(): Response
    {
        return Inertia::render('Validators/Validators');
    }
}
