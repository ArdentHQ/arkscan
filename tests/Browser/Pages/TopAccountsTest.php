<?php

declare(strict_types=1);

use App\Models\Wallet;
use App\Services\Cache\NetworkCache;
use Illuminate\Support\Facades\Cache;
use Laravel\Dusk\Browser;

beforeEach(function () {
    $this->withoutExceptionHandling();
});

afterEach(function () {
    Cache::flush();
});

it('should display the top accounts table', function ($resolution) {
    // Create wallets with different balances to test ordering
    Wallet::factory()->create(['balance' => 1000 * 1e18]);
    Wallet::factory()->create(['balance' => 500 * 1e18]);
    Wallet::factory()->create(['balance' => 250 * 1e18]);

    $networkCache = new NetworkCache();
    $networkCache->setSupply(fn () => 10000 * 1e18);

    $this->browse(function (Browser $browser) use ($resolution) {
        $browser->resize($resolution['width'], $resolution['height']);

        $browser->visitRoute('top-accounts')
            ->waitForText('Top Accounts')
            ->assertSee('Top Accounts')
            ->waitForText('1,000')
            ->assertSee('500')
            ->assertSee('250');
    });
})->with('resolutions');

it('should display wallets ordered by balance', function ($resolution) {
    $wallet1 = Wallet::factory()->create(['balance' => 100 * 1e18]);
    $wallet2 = Wallet::factory()->create(['balance' => 500 * 1e18]);
    $wallet3 = Wallet::factory()->create(['balance' => 300 * 1e18]);

    $networkCache = new NetworkCache();
    $networkCache->setSupply(fn () => 10000 * 1e18);

    $this->browse(function (Browser $browser) use ($resolution) {
        $browser->resize($resolution['width'], $resolution['height']);

        $browser->visitRoute('top-accounts')
            ->waitForText('Top Accounts')
            ->waitForText('500')
            ->assertSeeIn('table tbody tr:first-child', '500')
            ->assertSeeIn('table tbody tr:nth-child(2)', '300')
            ->assertSeeIn('table tbody tr:nth-child(3)', '100');
    });
})->with('desktop_resolutions');

it('should navigate to wallet page when clicking address', function ($resolution) {
    $wallet = Wallet::factory()->create(['balance' => 1000 * 1e18]);

    $networkCache = new NetworkCache();
    $networkCache->setSupply(fn () => 10000 * 1e18);

    $this->browse(function (Browser $browser) use ($resolution, $wallet) {
        $browser->resize($resolution['width'], $resolution['height']);

        $browser->visitRoute('top-accounts')
            ->waitForText('Top Accounts')
            ->waitForText('1,000')
            ->click('table tbody tr:first-child a')
            ->waitForRoute('wallet', ['wallet' => $wallet->address]);
    });
})->with('desktop_resolutions');

it('should paginate wallets', function ($resolution) {
    // Create 30 wallets to trigger pagination (25 per page)
    Wallet::factory()->count(30)->create();

    $networkCache = new NetworkCache();
    $networkCache->setSupply(fn () => 100000 * 1e18);

    $this->browse(function (Browser $browser) use ($resolution) {
        $browser->resize($resolution['width'], $resolution['height']);

        $browser->visitRoute('top-accounts')
            ->waitForText('Top Accounts')
            ->waitForText('Page 1 of 2')
            ->assertSee('Page 1 of 2');
    });
})->with('resolutions');

it('should display balance percentage', function ($resolution) {
    Wallet::factory()->create(['balance' => 1000 * 1e18]);

    $networkCache = new NetworkCache();
    $networkCache->setSupply(fn () => 10000 * 1e18);

    $this->browse(function (Browser $browser) use ($resolution) {
        $browser->resize($resolution['width'], $resolution['height']);

        $browser->visitRoute('top-accounts')
            ->waitForText('Top Accounts')
            ->waitForText('1,000')
            ->assertSee('10.00%');
    });
})->with('resolutions');

dataset('resolutions', [
    'desktop' => [['width' => 1280, 'height' => 1024]],
    'lg'      => [['width' => 1024, 'height' => 768]],
    'md-lg'   => [['width' => 960, 'height' => 667]],
    'md'      => [['width' => 768, 'height' => 1024]],
    'sm'      => [['width' => 640, 'height' => 960]],
    'xs'      => [['width' => 370, 'height' => 844]],
]);

dataset('desktop_resolutions', [
    'desktop' => [['width' => 1280, 'height' => 1024]],
    'lg'      => [['width' => 1024, 'height' => 768]],
    'md-lg'   => [['width' => 960, 'height' => 667]],
    'md'      => [['width' => 768, 'height' => 1024]],
]);
