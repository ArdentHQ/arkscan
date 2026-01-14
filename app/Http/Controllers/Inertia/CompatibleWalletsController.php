<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use App\Mail\WalletFormSubmitted;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

final class CompatibleWalletsController
{
    public function __invoke(): Response
    {
        return Inertia::renderWithMeta('Resources/CompatibleWallets', 'compatible-wallets', [
            'wallets' => array_values(trans('pages.compatible-wallets.wallets')),
        ]);
    }

    public function submit(Request $request): RedirectResponse
    {
        /** @phpstan-ignore-next-line */
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:50'],
            'website' => ['required', 'url'],
            'message' => ['nullable', 'string', 'max:500'],
        ]);

        Mail::send(new WalletFormSubmitted([
            'name'    => $data['name'],
            'website' => $data['website'],
            'message' => $data['message'] ?? null,
        ]));

        return redirect()->route('compatible-wallets');
    }
}
