<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\MultiPayment;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BigNumber;
use App\Services\Cache\MainsailCache;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\NetworkStatusBlockCache;
use App\Services\Cache\ValidatorCache;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Laravel\Dusk\Browser;

describe('Statistics', function () {
    it('should have statistics', function ($resolution) {
        Wallet::factory()->count(11)->create();

        $cache = new NetworkCache();

        $cache->setSupply(function (): float {
            return 12345.6789 * 1e18;
        });

        $cache->setVotesPercentage('123.45');
        $cache->setHeight(fn () => 123456);

        (new ValidatorCache())->setTotalBalanceVoted(4567.2345);
        (new MainsailCache())->setFees([
            'min' => '1500000000',
            'avg' => '2500000000',
            'max' => '3500000000',
        ]);

        $this->browse(function (Browser $browser) use ($resolution) {
            $browser->resize($resolution['width'], $resolution['height']);

            $outputInOrder = [
                trans('pages.home.statistics.total_supply'),
                '12K DARK',
                trans('pages.home.statistics.voting', ['percentage' => '123.45%']),
                '4K DARK',
                trans('pages.home.statistics.block_height'),
                '123,456',
            ];

            if ($resolution['width'] >= 640) {
                $outputInOrder = [
                    ...$outputInOrder,

                    trans('pages.home.statistics.gas_low'),
                    '1.5 Gwei',
                    trans('pages.home.statistics.gas_average'),
                    '2.5 Gwei',
                    trans('pages.home.statistics.gas_high'),
                    '3.5 Gwei',
                ];
            } else {
                $outputInOrder[] = trans('pages.home.statistics.gas_average_value', ['value' => '2.5 Gwei']);
            }

            $browser->visitRoute('home')
                ->waitForText(trans('pages.home.statistics.title_mobile'), ignoreCase: true)
                ->assertSeeInOrder($outputInOrder);

            if ($resolution['width'] < 640) {
                $browser->mouseover('[data-testid="statistics:gas-tracker"]')
                    ->assertSeeInOrder([
                        'Low:',
                        '~30 sec',
                        '1.5 Gwei',
                        'Average:',
                        '~30 sec',
                        '2.5 Gwei',
                        'High:',
                        '~30 sec',
                        '3.5 Gwei',
                    ]);
            }
        });
    })->with('resolutions');

    it('should calculate gas statistics with value', function ($resolution) {
        Wallet::factory()->count(11)->create();

        $cache = new NetworkCache();

        $cache->setSupply(function (): float {
            return 12345.6789 * 1e18;
        });

        $cache->setVotesPercentage('123.45');
        $cache->setHeight(fn () => 123456);

        (new ValidatorCache())->setTotalBalanceVoted(4567.2345);
        (new MainsailCache())->setFees([
            'min' => (string) BigNumber::new(1.5 * 1e18),
            'avg' => (string) BigNumber::new(2.5 * 1e18),
            'max' => (string) BigNumber::new(3.5 * 1e18),
        ]);

        (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', 2.0);

        $this->browse(function (Browser $browser) use ($resolution) {
            $browser->resize($resolution['width'], $resolution['height']);

            $outputInOrder = [
                trans('pages.home.statistics.total_supply'),
                '12K DARK',
                trans('pages.home.statistics.voting', ['percentage' => '123.45%']),
                '4K DARK',
                trans('pages.home.statistics.block_height'),
                '123,456',
            ];

            if ($resolution['width'] >= 640) {
                $outputInOrder = [
                    ...$outputInOrder,

                    trans('pages.home.statistics.gas_low'),
                    '$3.00',
                    trans('pages.home.statistics.gas_average'),
                    '$5.00',
                    trans('pages.home.statistics.gas_high'),
                    '$7.00',
                ];
            } else {
                $outputInOrder[] = trans('pages.home.statistics.gas_average_value', ['value' => '$5.00']);
            }

            dump($outputInOrder);

            $browser->visitRoute('home')
                ->pause(500)
                ->waitForText(trans('pages.home.statistics.title_mobile'), ignoreCase: true)
                ->assertSeeInOrder($outputInOrder);

            dump($outputInOrder);

            if ($resolution['width'] < 640) {
                $browser->mouseover('[data-testid="statistics:gas-tracker"]')
                    ->assertSeeInOrder([
                        'Low:',
                        '~30 sec',
                        '1500000000 Gwei',
                        'Average:',
                        '~30 sec',
                        '2500000000 Gwei',
                        'High:',
                        '~30 sec',
                        '3500000000 Gwei',
                    ]);
            } else {
                // sleep(3);
                $browser->driver->findElement(WebDriverBy::xpath('//div[text()="$3.00"]'))->hover();
            }
        });
    })->with('resolutions')->skip('Cannot change config/env values in test.');
});

describe('Transactions Tab', function () {
    beforeEach(function () {
        $this->wallet          = Wallet::factory()->create();
        $this->recipientWallet = Wallet::factory()->create();
    });

    it('should display transactions', function ($resolution) {
        $transactions = Transaction::factory()
            ->transfer()
            ->count(5)
            ->create([
                'from'              => $this->wallet->address,
                'to'                => $this->recipientWallet->address,
                'sender_public_key' => $this->wallet->public_key,
            ]);

        $this->browse(function (Browser $browser) use ($transactions, $resolution) {
            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('home')
                ->waitForText(substr($transactions[0]->hash, 0, 5));

            foreach ($transactions as $transaction) {
                $browser->assertSee(substr($transaction->hash, 0, 5));
            }
        });
    })->with('resolutions');

    it('should correctly format amounts', function (float $amount, string $expected, array $resolution) {
        $transaction = Transaction::factory()
            ->transfer()
            ->create([
                'value'             => BigNumber::new($amount * 1e18),
                'from'              => $this->wallet->address,
                'to'                => $this->wallet->address,
                'sender_public_key' => $this->wallet->public_key,
            ]);

        $this->browse(function (Browser $browser) use ($transaction, $resolution, $expected) {
            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('home')
                ->waitForText(substr($transaction->hash, 0, 5));

            $selector = '[data-testid="transaction:'.$transaction->hash.':amount"]';
            if ($resolution['width'] <= 640) {
                $selector = '[data-testid="transaction:mobile:'.$transaction->hash.':amount"]';
            }

            $browser->waitForTextIn($selector, $expected);
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
    ])
    ->with('resolutions');

    it('should correctly format multipayment transactions', function ($resolution) {
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

        $this->browse(function (Browser $browser) use ($transaction, $resolution) {
            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('home')
                ->waitForText(substr($transaction->hash, 0, 5));

            $selector = '[data-testid="transaction:'.$transaction->hash.':amount"]';
            if ($resolution['width'] <= 640) {
                $selector = '[data-testid="transaction:mobile:'.$transaction->hash.':amount"]';
            }

            $browser->waitForTextIn($selector, '579.5');
        });
    })->with('resolutions');
});

describe('Blocks Tab', function () {
    beforeEach(function () {
        $this->wallet = Wallet::factory()
            ->activeValidator()
            ->create();

        $this->wallet->setAttribute('attributes', [
            ...($this->wallet->getAttribute('attributes') ?? []),

            'username' => 'dusk-validator',
        ]);

        $this->wallet->save();
    });

    it('should display blocks', function ($resolution) {
        $blocks = Block::factory(10)->create();

        $this->browse(function (Browser $browser) use ($blocks, $resolution) {
            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('home', ['view' => 'blocks'])
                ->waitForText(number_format($blocks[0]->number->toNumber()));

            foreach ($blocks as $block) {
                $browser->assertSee(number_format($block->number->toNumber()));
            }
        });
    })->with('resolutions');

    it('should navigate to tab and back', function ($resolution) {
        $transactions = Transaction::factory()
            ->transfer()
            ->count(5)
            ->create([
                'from'              => $this->wallet->address,
                'to'                => $this->wallet->address,
                'sender_public_key' => $this->wallet->public_key,
            ]);

        $blocks = Block::factory(10)->create();

        $this->browse(function (Browser $browser) use ($transactions, $blocks, $resolution) {
            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('home')
                ->waitForText(substr($transactions[0]->hash, 0, 5))
                ->click('button#tab-blocks')
                ->waitForText(number_format($blocks[0]->number->toNumber()));

            foreach ($blocks as $block) {
                $browser->assertSee(number_format($block->number->toNumber()));
            }

            $browser->click('button#tab-transactions')
                ->waitForText(substr($transactions[0]->hash, 0, 5));

            foreach ($transactions as $transaction) {
                $browser->assertSee(substr($transaction->hash, 0, 5));
            }
        });
    })->with('resolutions');
});

dataset('resolutions', [
    'desktop' => [['width' => 1280, 'height' => 1024]],
    'lg'      => [['width' => 1024, 'height' => 768]],
    'md-lg'   => [['width' => 960, 'height' => 667]],
    'md'      => [['width' => 768, 'height' => 1024]],
    'sm'      => [['width' => 640, 'height' => 960]],
    'xs'      => [['width' => 370, 'height' => 844]],
]);
