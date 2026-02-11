<?php

declare(strict_types=1);

use App\Models\Scopes\OrderByTimestampScope;
use App\Models\TokenTransfer;
use App\Models\Wallet;
use App\Services\BigNumber;
use Laravel\Dusk\Browser;

beforeEach(function () {
    $this->wallet          = Wallet::factory()->create();
    $this->recipientWallet = Wallet::factory()->create();
});

it('should display transfers', function () {
    $transfers = TokenTransfer::factory(5)->create();

    $this->browse(function (Browser $browser) use ($transfers) {
        $browser->visitRoute('tokens.transfers');

        foreach ($this->resolutions as $resolution) {
            $browser->resize($resolution['width'], $resolution['height'])
                ->pause(100)
                ->waitForText('5 results', ignoreCase: true);

            foreach ($transfers as $transfer) {
                $browser->assertSee(substr($transfer->transaction_hash, 0, 5).'…'.substr($transfer->transaction_hash, -5));
            }
        }
    });
});

it('should correctly format amounts', function (float $amount, string $expected) {
    $transfer = TokenTransfer::factory()->create([
        'value' => (string) BigNumber::new($amount)->multipliedBy(1e18),
    ]);

    $this->browse(function (Browser $browser) use ($transfer, $expected) {
        $browser->visitRoute('tokens.transfers');

        foreach ($this->resolutions as $resolution) {
            $browser->resize($resolution['width'], $resolution['height'])
                ->pause(100)
                ->waitForText('1 result', ignoreCase: true)
                ->assertSee(substr($transfer->transaction_hash, 0, 5));

            $selector = '[data-testid="transaction:'.$transfer->transaction_hash.':amount"]';
            if ($resolution['width'] <= 640) {
                $selector = '[data-testid="transaction:mobile:'.$transfer->transaction_hash.':amount"]';
            }

            $browser->assertSeeIn($selector, $expected);
        }
    });
})
->with([
    '2'                => [2.34, '2.34'],
    '3'                => [2.345, '2.345'],
    '4'                => [2.3456, '2.3456'],
    '5'                => [2.34567, '2.34567'],
    '6'                => [2.345678, '2.345678'],
    '7'                => [2.3456789, '2.3456789'],
    '8'                => [2.34567891, '2.34567891'],
    '8 after rounding' => [2.345678915, '2.34567892'],
]);

it('should go to page 2', function ($resolution) {
    TokenTransfer::factory(50)->create();

    $this->browse(function (Browser $browser) use ($resolution) {
        $sortedTransfers = TokenTransfer::select('token_transfers.*')
            ->join('transactions', 'transactions.hash', '=', 'token_transfers.transaction_hash')
            ->withScope(OrderByTimestampScope::class);

        $browser->resize($resolution['width'], $resolution['height']);

        $browser->visitRoute('tokens.transfers')
            ->waitForText('50 results', ignoreCase: true)
            ->click('[data-testid="pagination:next-page"] button')
            ->waitForText('Page 2 of 2')
            ->assertQueryStringHas('page', '2');

        foreach ($sortedTransfers->skip(25)->take(5)->get() as $transfer) {
            $browser->assertSee(substr($transfer->transaction_hash, 0, 5).'…'.substr($transfer->transaction_hash, -5));
        }
    });
})->with('desktop_mobile_resolutions');

it('should reset to page 1 on per-page change', function ($resolution) {
    TokenTransfer::factory(50)->create();

    $this->browse(function (Browser $browser) use ($resolution) {
        $sortedTransfers = TokenTransfer::select('token_transfers.*')
            ->join('transactions', 'transactions.hash', '=', 'token_transfers.transaction_hash')
            ->withScope(OrderByTimestampScope::class);

        $browser->resize($resolution['width'], $resolution['height']);

        $browser->visitRoute('tokens.transfers', ['page' => 2])
            ->waitForText('50 results', ignoreCase: true)
            ->assertSee('Page 2 of 2')
            ->click('[data-testid="pagination:per-page-dropdown:button"]')
            ->waitForTextIn('[data-testid="pagination:per-page-dropdown:dropdown"]', '10')
            ->clickAtXPath('//div[@data-testid="pagination:per-page-dropdown:dropdown"]//div[normalize-space(text())="10"]')
            ->waitForText('Page 1 of 5');

        foreach ($sortedTransfers->take(10)->get() as $transfer) {
            $browser->assertSee(substr($transfer->transaction_hash, 0, 5).'…'.substr($transfer->transaction_hash, -5));
        }
    });
})->with('desktop_mobile_resolutions');

dataset('resolutions', [
    'desktop' => [['width' => 1280, 'height' => 1024]],
    'lg'      => [['width' => 1024, 'height' => 768]],
    'md-lg'   => [['width' => 960, 'height' => 667]],
    'md'      => [['width' => 768, 'height' => 1024]],
    'sm'      => [['width' => 640, 'height' => 960]],
    'xs'      => [['width' => 370, 'height' => 844]],
]);

dataset('desktop_mobile_resolutions', [
    'desktop' => [['width' => 1280, 'height' => 1024]],
    'xs'      => [['width' => 370, 'height' => 844]],
]);
