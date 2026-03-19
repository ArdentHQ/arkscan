<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use Inertia\Inertia;
use Inertia\Response;

final class BookmarksController
{
    public function __invoke(): Response
    {
        return Inertia::renderWithMeta('Bookmarks/Index', 'bookmarks');
    }
}
