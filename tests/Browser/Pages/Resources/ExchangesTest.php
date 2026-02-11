<?php

declare(strict_types=1);

use App\Enums\StatsPeriods;
use App\Facades\Network;
use App\Models\Exchange;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\NetworkStatusBlockCache;
use App\Services\Cache\PriceChartCache;
use App\Services\ExchangeRate;
use Carbon\Carbon;
use Facebook\WebDriver\Interactions\Internal\WebDriverCoordinates;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverPoint;
use Laravel\Dusk\Browser;

it('should show chart and switch between tabs', function ($resolution, $period, $prices, $minPrice, $maxPrice) {
    // No need to check chart on small screens for all periods
    if ($resolution['width'] < 640 && $period !== StatsPeriods::DAY) {
        return;
    }

    (new NetworkStatusBlockCache())->setPrice(Network::currency(), 'USD', 2.54);
    (new NetworkCache())->setSupply(fn () => 1_000_000.0 * 1e18);

    (new PriceChartCache())->setHistoricalRaw('USD', $period, collect([
        1_700_000_000 => $prices[0],
        1_700_003_600 => $prices[1],
        1_700_007_200 => $prices[2],
    ]));

    $this->browse(function (Browser $browser) use ($resolution, $period, $prices, $minPrice, $maxPrice) {
        $browser->resize($resolution['width'], $resolution['height']);

        $browser->visitRoute('dusk:exchanges:testing-can-be-exchanged', ['chartPeriod' => $period])
            ->waitForText('Exchange Listings');

        if ($resolution['width'] < 640) {
            $browser->assertMissing('[data-testid="exchanges:chart"]');
        } else {
            $browser->assertVisible('[data-testid="exchanges:chart"]')
                ->assertSee('$2.54')
                ->assertSeeInOrder([
                    'Market Cap',
                    '$2,540,000', 'USD',
                    'Min Price',
                    $minPrice,
                    'Max Price',
                    $maxPrice,
                ]);

            $element = $browser->resolver->findOrFail('canvas#exchanges-chart');

            $inViewPortCallback = static function () use ($element) {
                $baseLocation = $element->getLocationOnScreenOnceScrolledIntoView();

                return new WebDriverPoint(intval($baseLocation->getX() / 2), $baseLocation->getY());
            };

            $onPageCallback = static function () use ($element) {
                $baseLocation = $element->getLocation();

                return new WebDriverPoint(intval($baseLocation->getX() / 2), $baseLocation->getY());
            };

            $auxiliary = $element->getID();

            $coordinates = new WebDriverCoordinates(
                null,
                $inViewPortCallback,
                $onPageCallback,
                $auxiliary
            );

            $browser->driver->getMouse()->mouseMove($coordinates);

            $browserTimezone = $browser->driver->executeScript('return Intl.DateTimeFormat().resolvedOptions().timeZone;');

            $expectedTime = Carbon::createFromTimestamp(1_700_003_600)
                ->setTimezone($browserTimezone ?? 'UTC')
                ->format('d M Y H:i:s');

            $browser->waitForSeeInOrder([
                'Price:',
                '$'.number_format($prices[1], 2),
                $expectedTime,
            ]);
        }
    });
})->with('resolutions')->with([
    'day'     => [StatsPeriods::DAY, [123.0, 541.5, 331.2], '$123.00', '$541.50'],
    'week'    => [StatsPeriods::WEEK, [23.0, 541.5, 4431.2], '$23.00', '$4,431.20'],
    'month'   => [StatsPeriods::MONTH, [0.23, 8473.5, 331.2], '$0.23', '$8,473.50'],
    'quarter' => [StatsPeriods::QUARTER, [0.23, 8473.5, 331.2], '$0.23', '$8,473.50'],
    'all'     => [StatsPeriods::ALL, [14230.0, 541.5, 0.01], '$0.01', '$14,230.00'],
]);

it('should list exchanges', function ($resolution) {
    foreach (range(1, 15) as $index) {
        Exchange::factory()->create([
            'name'          => "Exchange {$index} Test",
            'url'           => "https://exchange-{$index}.com",
            'price'         => $index * 1.23,
            'volume'        => $index * 4567,
            'is_exchange'   => true,
            'is_aggregator' => true,
            'btc'           => true,
            'eth'           => true,
            'stablecoins'   => true,
            'other'         => true,
        ]);
    }

    (new NetworkStatusBlockCache())->setPrice(Network::currency(), 'USD', 2.5);

    $this->browse(function (Browser $browser) use ($resolution) {
        $browser->resize($resolution['width'], $resolution['height']);

        $browser->visitRoute('exchanges')
            ->waitForText('Exchange Listings');

        foreach (Exchange::all() as $exchange) {
            $data = [
                $exchange->name,
                'BTC',
                'ETH',
                'Stablecoins',
                'Other',
                ExchangeRate::convertFiatToCurrency($exchange->price, 'USD', 'USD'),
            ];

            if ($resolution['width'] > 768) {
                $data[] = ExchangeRate::convertFiatToCurrency($exchange->volume, 'USD', 'USD', 2);
            }

            $browser->assertSeeInOrder($data);

            expect($browser->driver->findElements(WebDriverBy::xpath('//a[@href="'.$exchange->url.'"]')))->toHaveCount(2); // One for Desktop and one for Mobile
        }
    });
})->with('resolutions');

it('should submit modal', function ($resolution) {
    $this->browse(function (Browser $browser) use ($resolution) {
        $browser->resize($resolution['width'], $resolution['height']);

        $browser->visitRoute('exchanges')
            ->waitForText('Exchange Listings')
            ->clickLink('Submit Exchange', 'button')
            ->assertSee('Submit a Listing')
            ->value('input[name="name"]', 'My Exchange')
            ->value('input[name="website"]', 'https://myexchange.com')
            ->type('input[name="pairs"]', 'BTC, ETH, USDT')
            ->value('textarea[name="message"]', 'I would like to submit my exchange for listing.')
            ->assertAttributeMissing('button[type="submit"]', 'disabled')
            ->click('button[type="submit"]')
            ->waitForText(trans('pages.exchanges.submit-modal.success_toast'));
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
