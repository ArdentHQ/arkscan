<?php

declare(strict_types=1);

use App\Models\Wallet;
use App\Services\Cache\MainsailCache;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\ValidatorCache;
use Illuminate\Support\Facades\Cache;
use Laravel\Dusk\Browser;

beforeEach(function () {
    $this->withoutExceptionHandling();
});

afterEach(function () {
    Cache::flush();
});

it('should display highlights', function ($resolution) {
    Wallet::factory()->count(15)->create();

    // Register 3 validators
    $validators = Wallet::factory()
        ->count(3)
        ->activeValidator()
        ->create();

    $networkCache = new NetworkCache();
    $networkCache->setSupply(fn () => 500000000 * 1e18);
    $networkCache->setVotesPercentage('75.50');
    $networkCache->setValidatorRegistrationCount(3);

    (new ValidatorCache())->setTotalBalanceVoted(250000000);
    (new MainsailCache())->setFees([
        'min' => '1000000000',
        'avg' => '2000000000',
        'max' => '5000000000',
    ]);

    $this->browse(function (Browser $browser) use ($resolution) {
        $browser->resize($resolution['width'], $resolution['height']);

        $browser->visitRoute('statistics')
            ->waitForText('Statistics')
            ->waitForText('Current Gas Prices')
            ->assertSee('500,000,000 DARK')
            ->assertSee('250,000,000 DARK')
            ->assertSee('75.50%')
            ->assertSee('18');
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
