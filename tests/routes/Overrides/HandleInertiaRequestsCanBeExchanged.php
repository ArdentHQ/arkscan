<?php

declare(strict_types=1);

namespace Tests\routes\Overrides;

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

/**
 * @coversNothing
 */
class HandleInertiaRequestsCanBeExchanged extends HandleInertiaRequests
{
    public function share(Request $request): array
    {
        Config::set('arkscan.networks.development.canBeExchanged', true);

        return parent::share($request);
    }
}
