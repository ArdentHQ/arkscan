<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Models\Wallet;
use Laravel\Dusk\Browser;

beforeEach(function () {
    $this->withoutExceptionHandling();

    $this->wallet          = Wallet::factory()->create();
    $this->recipientWallet = Wallet::factory()->create();
});

it('should show the default currency in the price ticker', function () {
    Transaction::factory()
        ->transfer()
        ->count(3)
        ->create([
            'from'              => $this->wallet->address,
            'to'                => $this->recipientWallet->address,
            'sender_public_key' => $this->wallet->public_key,
        ]);

    $this->browse(function (Browser $browser) {
        $browser->resize(1280, 1024)
            ->visitRoute('transactions')
            ->waitForText('3 results', ignoreCase: true)
            ->assertSeeIn('[data-testid="price-ticker:currency"]', 'USD');
    });
});

it('should persist the selected currency after page reload', function () {
    Transaction::factory()
        ->transfer()
        ->count(3)
        ->create([
            'from'              => $this->wallet->address,
            'to'                => $this->recipientWallet->address,
            'sender_public_key' => $this->wallet->public_key,
        ]);

    $this->browse(function (Browser $browser) {
        $browser->resize(1280, 1024)
            ->visitRoute('transactions')
            ->waitForText('3 results', ignoreCase: true)
            ->assertSeeIn('[data-testid="price-ticker:currency"]', 'USD');

        // Change currency via fetch (simulates what the Inertia router does under the hood)
        $browser->script("
            const csrf = document.head.querySelector('meta[name=\"csrf-token\"]').content;
            fetch('/currency/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': csrf,
                },
                redirect: 'follow',
                body: new URLSearchParams({ currency: 'EUR', _token: csrf }),
            }).then(() => window.location.reload());
        ");

        $browser->waitForText('3 results', ignoreCase: true)
            ->assertSeeIn('[data-testid="price-ticker:currency"]', 'EUR');

        // Reset to USD for other tests
        $browser->script("
            const csrf = document.head.querySelector('meta[name=\"csrf-token\"]').content;
            fetch('/currency/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': csrf,
                },
                redirect: 'follow',
                body: new URLSearchParams({ currency: 'USD', _token: csrf }),
            });
        ");
    });
});

dataset('resolutions', [
    'desktop' => [['width' => 1280, 'height' => 1024]],
    'lg'      => [['width' => 1024, 'height' => 768]],
    'md-lg'   => [['width' => 960, 'height' => 667]],
    'md'      => [['width' => 768, 'height' => 1024]],
    'sm'      => [['width' => 640, 'height' => 960]],
    'xs'      => [['width' => 370, 'height' => 844]],
]);
