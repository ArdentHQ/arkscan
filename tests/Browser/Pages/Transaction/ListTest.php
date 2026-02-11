<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Models\MultiPayment;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Scopes\OrderByTransactionIndexScope;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BigNumber;
use App\Services\NumberFormatter;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Facades\Cache;
use Laravel\Dusk\Browser;

beforeEach(function () {
    $this->withoutExceptionHandling();

    $this->wallet          = Wallet::factory()->create();
    $this->recipientWallet = Wallet::factory()->create();
});

it('should display transactions', function () {
    $transactions = Transaction::factory()
        ->transfer()
        ->count(5)
        ->create([
            'from'              => $this->wallet->address,
            'to'                => $this->recipientWallet->address,
            'sender_public_key' => $this->wallet->public_key,
        ]);

    $this->browse(function (Browser $browser) use ($transactions) {
        $browser->visitRoute('transactions');

        foreach ($this->resolutions as $resolution) {
            $browser->resize($resolution['width'], $resolution['height'])
                ->pause(100)
                ->waitForText('5 results', ignoreCase: true);

            foreach ($transactions as $transaction) {
                $browser->assertSee(substr($transaction->hash, 0, 5));
            }
        }
    });
});

it('should correctly format amounts', function (float $amount, string $expected) {
    $transaction = Transaction::factory()
        ->transfer()
        ->create([
            'value'             => BigNumber::new($amount * 1e18),
            'from'              => $this->wallet->address,
            'to'                => $this->wallet->address,
            'sender_public_key' => $this->wallet->public_key,
        ]);

    $this->browse(function (Browser $browser) use ($transaction, $expected) {
        $browser->visitRoute('transactions');

        foreach ($this->resolutions as $resolution) {
            $browser->resize($resolution['width'], $resolution['height'])
                ->pause(100)
                ->waitForText('1 result', ignoreCase: true)
                ->assertSee(substr($transaction->hash, 0, 5));

            $selector = '[data-testid="transaction:'.$transaction->hash.':amount"]';
            if ($resolution['width'] <= 640) {
                $selector = '[data-testid="transaction:mobile:'.$transaction->hash.':amount"]';
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

it('should correctly format multipayment transactions', function () {
    $transaction = Transaction::factory()
        ->multiPayment(
            [
                $this->wallet->address,
                $this->recipientWallet->address,
            ],
            [
                BigNumber::new(123.5 * 1e18),
                BigNumber::new(456 * 1e18),
            ],
        )
        ->create([
            'from'              => $this->wallet->address,
            'sender_public_key' => $this->wallet->public_key,
        ]);

    MultiPayment::factory()
        ->count(2)
        ->state(new Sequence(
            [
                'to'     => $this->wallet->address,
                'amount' => BigNumber::new(123.5 * 1e18),
            ],
            [
                'to'     => $this->recipientWallet->address,
                'amount' => BigNumber::new(456 * 1e18),
            ],
        ))
        ->create([
            'from' => $transaction->from,
            'hash' => $transaction->hash,
        ]);

    $this->browse(function (Browser $browser) use ($transaction) {
        foreach ($this->resolutions as $resolution) {
            $browser->visitRoute('transactions')
                ->resize($resolution['width'], $resolution['height'])
                ->waitForText('1 result', ignoreCase: true)
                ->assertSee(substr($transaction->hash, 0, 5));

            $selector = '[data-testid="transaction:'.$transaction->hash.':amount"]';
            if ($resolution['width'] <= 640) {
                $selector = '[data-testid="transaction:mobile:'.$transaction->hash.':amount"]';
            }

            $browser->waitForTextIn($selector, '579');
        }
    });
});

it('should go to page 2', function ($resolution) {
    Transaction::factory()
        ->transfer()
        ->count(30)
        ->create([
            'from'              => $this->wallet->address,
            'to'                => $this->recipientWallet->address,
            'sender_public_key' => $this->wallet->public_key,
        ]);

    $this->browse(function (Browser $browser) use ($resolution) {
        $sortedTransactions = Transaction::withScope(OrderByTimestampScope::class)
            ->withScope(OrderByTransactionIndexScope::class);

        $browser->resize($resolution['width'], $resolution['height']);

        $browser->visitRoute('transactions')
            ->waitForText('30 results', ignoreCase: true)
            ->click('[data-testid="pagination:next-page"] button')
            ->waitForText('Page 2 of 2')
            ->assertQueryStringHas('page', '2');

        foreach ($sortedTransactions->skip(25)->take(5)->get() as $transaction) {
            $browser->assertSee(substr($transaction->hash, 0, 5));
        }
    });
})->with('resolutions');

it('should reset to page 1 on per-page change', function ($resolution) {
    Transaction::factory()
        ->transfer()
        ->count(30)
        ->create([
            'from'              => $this->wallet->address,
            'to'                => $this->recipientWallet->address,
            'sender_public_key' => $this->wallet->public_key,
        ]);

    $this->browse(function (Browser $browser) use ($resolution) {
        $sortedTransactions = Transaction::withScope(OrderByTimestampScope::class)
            ->withScope(OrderByTransactionIndexScope::class);

        $browser->resize($resolution['width'], $resolution['height']);

        $browser->visitRoute('transactions', ['page' => 2])
            ->waitForText('30 results', ignoreCase: true)
            ->assertSee('Page 2 of 2')
            ->click('[data-testid="pagination:per-page-dropdown:button"]')
            ->waitForTextIn('[data-testid="pagination:per-page-dropdown:dropdown"]', '10')
            ->clickAtXPath('//div[@data-testid="pagination:per-page-dropdown:dropdown"]//div[normalize-space(text())="10"]')
            ->waitForText('Page 1 of 3');

        foreach ($sortedTransactions->take(10)->get() as $transaction) {
            $browser->assertSee(substr($transaction->hash, 0, 5));
        }
    });
})->with('resolutions');

it('should open the filter', function () {
    Transaction::factory()
        ->transfer()
        ->count(5)
        ->create([
            'from'              => $this->wallet->address,
            'to'                => $this->recipientWallet->address,
            'sender_public_key' => $this->wallet->public_key,
        ]);

    $this->browse(function (Browser $browser) {
        $browser->visitRoute('transactions')
            ->waitForText('5 results', ignoreCase: true)
            ->click('[data-testid="transactions:filter:button"]')
            ->waitForText('Select All');

        foreach ($this->resolutions as $resolution) {
            $browser->resize($resolution['width'], $resolution['height'])
                ->pause(100);

            expect($browser->driver->findElements(WebDriverBy::xpath('//div[contains(@class, "dropdown")]//label[text()="Select All"]')))->toHaveCount(1);
            expect($browser->driver->findElements(WebDriverBy::xpath('//div[contains(@class, "dropdown")]//label[text()="Transfers"]')))->toHaveCount(1);
            expect($browser->driver->findElements(WebDriverBy::xpath('//div[contains(@class, "dropdown")]//label[text()="Multipayments"]')))->toHaveCount(1);
            expect($browser->driver->findElements(WebDriverBy::xpath('//div[contains(@class, "dropdown")]//label[text()="Votes"]')))->toHaveCount(1);
            expect($browser->driver->findElements(WebDriverBy::xpath('//div[contains(@class, "dropdown")]//label[text()="Validator"]')))->toHaveCount(1);
            expect($browser->driver->findElements(WebDriverBy::xpath('//div[contains(@class, "dropdown")]//label[text()="Username"]')))->toHaveCount(1);
            expect($browser->driver->findElements(WebDriverBy::xpath('//div[contains(@class, "dropdown")]//label[text()="Contract Deployment"]')))->toHaveCount(1);
            expect($browser->driver->findElements(WebDriverBy::xpath('//div[contains(@class, "dropdown")]//label[text()="Others"]')))->toHaveCount(1);
        }
    });
});

it('should track querystring for filters', function () {
    Transaction::factory()
        ->transfer()
        ->create([
            'from'              => $this->wallet->address,
            'to'                => $this->recipientWallet->address,
            'sender_public_key' => $this->wallet->public_key,
        ]);

    $this->browse(function (Browser $browser) {
        $browser->resize(1280, 800)
            ->visitRoute('transactions')
            ->waitForText('1 result', ignoreCase: true)
            ->click('[data-testid="transactions:filter:button"]')
            ->waitForText('Select All');

        $browser->driver->findElements(WebDriverBy::xpath('//div[contains(@class, "dropdown")]//label[text()="Transfers"]'))[0]->click();

        $browser->waitForQueryString('transfers', 'false');

        $browser->driver->findElements(WebDriverBy::xpath('//div[contains(@class, "dropdown")]//label[text()="Multipayments"]'))[0]->click();

        $browser->waitForQueryString('multipayments', 'false');

        $browser->driver->findElements(WebDriverBy::xpath('//div[contains(@class, "dropdown")]//label[text()="Votes"]'))[0]->click();

        $browser->waitForQueryString('votes', 'false');

        $browser->driver->findElements(WebDriverBy::xpath('//div[contains(@class, "dropdown")]//label[text()="Validator"]'))[0]->click();

        $browser->waitForQueryString('validator', 'false');

        $browser->driver->findElements(WebDriverBy::xpath('//div[contains(@class, "dropdown")]//label[text()="Username"]'))[0]->click();

        $browser->waitForQueryString('username', 'false');

        $browser->driver->findElements(WebDriverBy::xpath('//div[contains(@class, "dropdown")]//label[text()="Contract Deployment"]'))[0]->click();

        $browser->waitForQueryString('contract_deployment', 'false');

        $browser->driver->findElements(WebDriverBy::xpath('//div[contains(@class, "dropdown")]//label[text()="Others"]'))[0]->click();

        $browser->waitForQueryString('others', 'false');
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
