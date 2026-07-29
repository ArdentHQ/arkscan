<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use Inertia\Inertia;
use Inertia\Response;

final class SupportController
{
    public function __invoke(): Response
    {
        return Inertia::renderWithMeta('Support/Index', 'support', [
            'socialNetworkUrls' => [
                'twitter' => config('social.networks.twitter.url'),
                'github'  => config('social.networks.github.url'),
            ],

            'contactEmail' => config('mail.contact_email'),
        ]);
    }
}
