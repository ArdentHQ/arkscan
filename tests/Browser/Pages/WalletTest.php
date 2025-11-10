<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Scopes\OrderByTransactionIndexScope;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Addresses\Legacy;
use App\Services\Cache\WalletCache;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Support\Facades\Cache;
use Laravel\Dusk\Browser;

beforeEach(function () {
    $this->withoutExceptionHandling();
});

afterEach(function () {
    Cache::tags(['dusk'])->flush();
});

describe('Overview', function () {
    it('should display wallet overview information', function ($resolution) {
        $validator = Wallet::factory()
            ->activeValidator()
            ->create();

        $wallet = Wallet::factory()
            ->create([
                'balance'    => 32423 * 1e18,
                'attributes' => [
                    'username' => 'joe.blogs',
                    'vote'     => $validator->address,
                ],
            ]);

        (new WalletCache())->setVote($validator->address, $validator);

        $this->browse(function (Browser $browser) use ($wallet, $validator, $resolution) {
            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('wallet', $wallet)
                ->waitForText(substr($wallet->address, 0, 7))
                ->assertSee('joe.blogs')
                ->assertSee('32,423 DARK')
                ->assertSee(substr($validator->address, 0, 7));
        });
    })->with('resolutions');

    it('should handle balance with many decimal places', function ($resolution) {
        $wallet = Wallet::factory()
            ->create([
                'balance' => 32423.32465432 * 1e18,
            ]);

        $this->browse(function (Browser $browser) use ($wallet, $resolution) {
            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('wallet', $wallet)
                ->waitForText(substr($wallet->address, 0, 7));

            if ($resolution['width'] >= 640) {
                $browser->assertSee('32,423.32465432 DARK');
            } else {
                $browser->assertSee('32,423.32 DARK');
            }
        });
    })->with('resolutions');

    it('should copy address to clipboard', function ($resolution) {
        $wallet = Wallet::factory()->create();

        $this->browse(function (Browser $browser) use ($wallet, $resolution) {
            $this->grantPermission($browser, ['clipboardReadWrite', 'clipboardSanitizedWrite']);

            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('wallet', $wallet)
                ->waitForText(substr($wallet->address, 0, 7))
                ->click('[data-testid="wallet:copy-address"] button')
                ->waitForText(trans('pages.wallet.address_copied'));

            $browser->assertScript('navigator.clipboard.readText()', $wallet->address);
        });
    })->with('resolutions');

    it('should open public key modal & copy it to clipboard', function ($resolution) {
        $wallet = Wallet::factory()->create();

        $this->browse(function (Browser $browser) use ($wallet, $resolution) {
            $this->grantPermission($browser, ['clipboardReadWrite', 'clipboardSanitizedWrite']);

            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('wallet', $wallet)
                ->waitForText(substr($wallet->address, 0, 7))
                ->click('[data-testid="wallet:show-public-key"] > button')
                ->waitForText(substr($wallet->public_key, 0, 7))
                ->click('[data-testid="wallet:show-public-key:clipboard"] button')
                ->waitForText(trans('pages.wallet.copied_public_key'))
                ->click('button[data-testid="wallet:show-public-key:close"]')
                ->assertDontSee(substr($wallet->public_key, 0, 7));

            $browser->assertScript('navigator.clipboard.readText()', $wallet->public_key);
        });
    })->with('resolutions');

    it('should open legacy address modal & copy it to clipboard', function ($resolution) {
        $wallet = Wallet::factory()
            ->create([
                'attributes' => [
                    'isLegacy' => true,
                ],
            ]);

        $legacyAddress = Legacy::generateAddressFromPublicKey($wallet->public_key);

        $this->browse(function (Browser $browser) use ($wallet, $legacyAddress, $resolution) {
            $this->grantPermission($browser, ['clipboardReadWrite', 'clipboardSanitizedWrite']);

            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('wallet', $wallet)
                ->waitForText(substr($wallet->address, 0, 7))
                ->click('[data-testid="wallet:show-legacy-address"] > button')
                ->waitForText(substr($legacyAddress, 0, 7))
                ->click('[data-testid="wallet:show-legacy-address:clipboard"] button')
                ->waitForText(trans('pages.wallet.legacy_address_copied'))
                ->click('button[data-testid="wallet:show-legacy-address:close"]')
                ->assertDontSee(substr($legacyAddress, 0, 7));

            $browser->assertScript('navigator.clipboard.readText()', $legacyAddress);
        });
    })->with('resolutions');

    it('should open qr code modal & expand amount options', function ($resolution) {
        $wallet = Wallet::factory()->create();

        $this->browse(function (Browser $browser) use ($wallet, $resolution) {
            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('wallet', $wallet)
                ->waitForText(substr($wallet->address, 0, 7))
                ->click('[data-testid="wallet:show-qr-code-modal:button"]')
                ->waitForText(trans('pages.wallet.qrcode.title'))
                ->clickAtXPath('//button[.//text()="Specify Amount"]')
                ->waitForText(trans('pages.wallet.qrcode.description'));
        });
    })->with('resolutions');

    it('should show correct validator status', function ($factoryMethod, $status, $resolution) {
        $wallet = Wallet::factory()
            ->{$factoryMethod}()
            ->create();

        $this->browse(function (Browser $browser) use ($wallet, $status, $resolution) {
            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('wallet', $wallet)
                ->waitForText(substr($wallet->address, 0, 7))
                ->assertSee(trans('pages.validators.'.$status));
        });
    })->with([
        'active'   => ['activeValidator', 'active'],
        'standby'  => ['standbyValidator', 'standby'],
        'dormant'  => ['dormantValidator', 'dormant'],
        'resigned' => ['resignedValidator', 'resigned'],
    ])->with('resolutions');

    it('should track querystring between tabs', function () {
        $this->wallet = Wallet::factory()
            ->activeValidator()
            ->create();

        Transaction::factory()
            ->transfer()
            ->count(52)
            ->create([
                'from'              => $this->wallet->address,
                'to'                => $this->wallet->address,
                'sender_public_key' => $this->wallet->public_key,
            ]);

        Block::factory()
            ->count(30)
            ->create([
                'proposer' => $this->wallet->address,
            ]);

        $this->browse(function (Browser $browser) {
            $browser->resize(1280, 800)
                ->visitRoute('wallet', $this->wallet)
                ->waitForText('52 result', ignoreCase: true)
                ->click('[data-testid="wallet:transactions:filter:button"]')
                ->waitForText('Select All');

            $browser->driver->findElements(WebDriverBy::xpath('//div[contains(@class, "dropdown")]//label[text()="Multipayments"]'))[0]->click();
            $browser->waitForQueryString('multipayments', 'false')->pause(100);
            $browser->driver->findElements(WebDriverBy::xpath('//div[contains(@class, "dropdown")]//label[text()="Votes"]'))[0]->click();
            $browser->waitForQueryString('votes', 'false')->pause(100);
            $browser->driver->findElements(WebDriverBy::xpath('//div[contains(@class, "dropdown")]//label[text()="Validator"]'))[0]->click();
            $browser->waitForQueryString('validator', 'false')->pause(100);

            $browser->click('[data-testid="pagination:next-page"] button')
                ->pause(100)
                ->waitForText('Page 2 of 3')
                ->click('[data-testid="pagination:next-page"] button')
                ->pause(100)
                ->waitForText('Page 3 of 3')
                ->assertQueryStringHas('page', '3');

            $browser->script('window.scrollTo(0, 0);');

            $browser->click('button#tab-blocks')
                ->waitForText('30 results', ignoreCase: true)
                ->assertQueryStringMissing('page')
                ->assertQueryStringMissing('per-page')
                ->assertQueryStringMissing('multipayments')
                ->assertQueryStringMissing('votes')
                ->assertQueryStringMissing('validator');

            $browser->script('window.scrollTo(0, 0);');

            $browser->click('[data-testid="pagination:next-page"] button')
                ->pause(100)
                ->waitForText('Page 2 of 2')
                ->assertQueryStringMissing('multipayments')
                ->assertQueryStringMissing('votes')
                ->assertQueryStringMissing('validator')
                ->assertQueryStringMissing('per-page')
                ->assertQueryStringHas('page', '2');

            $browser->script('window.scrollTo(0, 0);');

            $browser->click('button#tab-transactions')
                ->pause(100)
                ->waitForText('52 results', ignoreCase: true)
                ->assertSee('Page 3 of 3')
                ->assertQueryStringHas('multipayments', 'false')
                ->assertQueryStringHas('votes', 'false')
                ->assertQueryStringHas('validator', 'false')
                ->assertQueryStringHas('page', '3')
                ->assertQueryStringMissing('per-page');
        });
    });
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

            $browser->visitRoute('wallet', $this->wallet)
                ->waitForText('5 results', ignoreCase: true);

            foreach ($transactions as $transaction) {
                $browser->assertSee(substr($transaction->hash, 0, 5));
            }
        });
    })->with('resolutions');

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
            $sortedTransactions = Transaction::where('from', $this->wallet->address)
                ->withScope(OrderByTimestampScope::class)
                ->withScope(OrderByTransactionIndexScope::class);

            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('wallet', $this->wallet)
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
            $sortedTransactions = Transaction::where('from', $this->wallet->address)
                ->withScope(OrderByTimestampScope::class)
                ->withScope(OrderByTransactionIndexScope::class);

            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('wallet', ['wallet' => $this->wallet, 'page' => 2])
                ->waitForText('30 results', ignoreCase: true)
                ->assertSee('Page 2 of 2')
                ->click('[data-testid="pagination:per-page-dropdown:button"]')
                ->waitForTextIn('[data-testid="pagination:per-page-dropdown:dropdown"]', '10')
                ->clickAtXPath('//div[@data-testid="pagination:per-page-dropdown:dropdown"]//span[.//text()="10"]')
                ->waitForText('Page 1 of 3');

            foreach ($sortedTransactions->take(10)->get() as $transaction) {
                $browser->assertSee(substr($transaction->hash, 0, 5));
            }
        });
    })->with('resolutions');

    it('should export transactions from the modal', function ($resolution) {
        Transaction::factory()
            ->transfer()
            ->count(3)
            ->create([
                'from'              => $this->wallet->address,
                'to'                => $this->recipientWallet->address,
                'sender_public_key' => $this->wallet->public_key,
            ]);

        // @see tests/routes/dusk.php
        Cache::tags(['dusk'])->set('dusk.transactions_response', [
            'data' => [
                [
                    'hash'      => 'dusk-transaction-1',
                    'timestamp' => [
                        'epoch' => now()->timestamp,
                    ],
                    'from'  => $this->wallet->address,
                    'to'    => $this->recipientWallet->address,
                    'value' => '125000000',
                    'fee'   => '1000000',
                ],
            ],
            'meta' => [
                'count' => 1,
            ],
        ]);

        $description    = trans('pages.wallet.export-transactions-modal.description');
        $successMessage = trans('pages.wallet.export-transactions-modal.success_message', ['count' => 1]);

        $this->browse(function (Browser $browser) use ($resolution, $description, $successMessage) {
            $downloadName     = sprintf('%s.csv', $this->wallet->address);
            $downloadSelector = '[data-testid="wallet:transactions-export:download"]';

            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('wallet', $this->wallet)
                ->waitForText('3 results', ignoreCase: true)
                ->click('[data-testid="wallet:transactions:export-button"]')
                ->waitForText($description)
                ->assertDisabled('[data-testid="wallet:transactions-export:submit"]')
                ->click('[data-testid="wallet:transactions-export:types-trigger"]')
                ->click('[data-testid="wallet:transactions-export:type-transfers"]')
                ->click('[data-testid="wallet:transactions-export:types-trigger"]')
                ->click('[data-testid="wallet:transactions-export:columns-trigger"]')
                ->click('[data-testid="wallet:transactions-export:column-id"]')
                ->click('[data-testid="wallet:transactions-export:columns-trigger"]')
                ->assertEnabled('[data-testid="wallet:transactions-export:submit"]')
                ->click('[data-testid="wallet:transactions-export:submit"]')
                ->waitForText($successMessage)
                ->assertVisible($downloadSelector)
                ->assertAttribute($downloadSelector, 'download', $downloadName);

            expect($browser->attribute($downloadSelector, 'href'))->toStartWith('data:text/csv');
        });
    })->with('resolutions');

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
                ->visitRoute('wallet', $this->wallet)
                ->waitForText('1 result', ignoreCase: true)
                ->click('[data-testid="wallet:transactions:filter:button"]')
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
});

describe('Blocks Tab', function () {
    beforeEach(function () {
        $this->wallet = Wallet::factory()
            ->activeValidator()
            ->create();

        $attributes             = $this->wallet->getAttribute('attributes') ?? [];
        $attributes['username'] = 'dusk-validator';
        $this->wallet->setAttribute('attributes', $attributes);
        $this->wallet->save();
    });

    it('should navigate to tab and back', function ($resolution) {
        $transactions = Transaction::factory()
            ->transfer()
            ->count(5)
            ->create([
                'from'              => $this->wallet->address,
                'to'                => $this->wallet->address,
                'sender_public_key' => $this->wallet->public_key,
            ]);

        $blocks = Block::factory()
            ->count(6)
            ->create([
                'proposer' => $this->wallet->address,
            ]);

        $this->browse(function (Browser $browser) use ($transactions, $blocks, $resolution) {
            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('wallet', $this->wallet)
                ->waitForText('5 results', ignoreCase: true);

            if ($resolution['width'] < 768) {
                $browser->click('[data-testid="tabs:dropdown:button"]')
                    ->waitForText('Validated Blocks')
                    ->clickAtXPath('//div[@data-testid="tabs:dropdown:dropdown"]//span[.//text()="Validated Blocks"]');
            } else {
                $browser->click('button#tab-blocks');
            }

            $browser->waitForText('6 results', ignoreCase: true);

            foreach ($blocks as $block) {
                $browser->assertSee(number_format($block->number->toNumber()));
            }

            if ($resolution['width'] < 768) {
                $browser->click('[data-testid="tabs:dropdown:button"]')
                    ->waitForText('Transactions')
                    ->clickAtXPath('//div[@data-testid="tabs:dropdown:dropdown"]//span[.//text()="Transactions"]');
            } else {
                $browser->click('button#tab-transactions');
            }

            $browser->waitForText('5 results', ignoreCase: true);

            foreach ($transactions as $transaction) {
                $browser->assertSee(substr($transaction->hash, 0, 5));
            }
        });
    })->with('resolutions');

    it('should show tab on page load from query string', function ($resolution) {
        $transactions = Transaction::factory()
            ->transfer()
            ->count(5)
            ->create([
                'from'              => $this->wallet->address,
                'to'                => $this->wallet->address,
                'sender_public_key' => $this->wallet->public_key,
            ]);

        $blocks = Block::factory()
            ->count(6)
            ->create([
                'proposer' => $this->wallet->address,
            ]);

        $this->browse(function (Browser $browser) use ($transactions, $blocks, $resolution) {
            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('wallet', ['wallet' => $this->wallet, 'tab' => 'blocks'])
                ->waitForText('6 results', ignoreCase: true);

            foreach ($blocks as $block) {
                $browser->assertSee(number_format($block->number->toNumber()));
            }

            if ($resolution['width'] < 768) {
                $browser->click('[data-testid="tabs:dropdown:button"]')
                    ->waitForText('Transactions')
                    ->clickAtXPath('//div[@data-testid="tabs:dropdown:dropdown"]//span[.//text()="Transactions"]');
            } else {
                $browser->click('button#tab-transactions');
            }

            $browser->waitForText('5 results', ignoreCase: true);

            foreach ($transactions as $transaction) {
                $browser->assertSee(substr($transaction->hash, 0, 5));
            }
        });
    })->with('resolutions');

    it('should export blocks from the modal', function ($resolution) {
        Transaction::factory()
            ->transfer()
            ->count(2)
            ->create([
                'from'              => $this->wallet->address,
                'to'                => $this->wallet->address,
                'sender_public_key' => $this->wallet->public_key,
            ]);

        Block::factory()
            ->count(2)
            ->create([
                'proposer' => $this->wallet->address,
            ]);

        // @see tests/routes/dusk.php
        Cache::tags(['dusk'])->set('dusk.blocks_response', [
            'data' => [
                [
                    'hash'               => 'dusk-block-1',
                    'timestamp'          => (string) (now()->timestamp * 1000),
                    'transactionsCount'  => 12,
                    'amount'             => '6400000000',
                    'fee'                => '50000000',
                    'reward'             => '200000000',
                    'number'             => 123456789,
                ],
            ],
            'meta' => [
                'count' => 1,
            ],
        ]);

        $description    = trans('pages.wallet.export-blocks-modal.description');
        $successMessage = 'A total of 1 blocks have been retrieved and are ready for download.';

        $this->browse(function (Browser $browser) use ($resolution, $description, $successMessage) {
            $downloadName     = 'dusk-validator.csv';
            $downloadSelector = '[data-testid="wallet:blocks-export:download"]';

            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('wallet', $this->wallet)
                ->waitForText('2 results', ignoreCase: true);

            if ($resolution['width'] < 768) {
                $browser->click('[data-testid="tabs:dropdown:button"]')
                    ->waitForText('Validated Blocks')
                    ->clickAtXPath('//div[@data-testid="tabs:dropdown:dropdown"]//span[.//text()="Validated Blocks"]');
            } else {
                $browser->click('button#tab-blocks');
            }

            $browser->waitForText('2 results', ignoreCase: true)
                ->click('[data-testid="wallet:blocks:export-button"]')
                ->waitForText($description)
                ->assertDisabled('[data-testid="wallet:blocks-export:submit"]')
                ->click('[data-testid="wallet:blocks-export:columns-trigger"]')
                ->click('[data-testid="wallet:blocks-export:column-id"]')
                ->click('[data-testid="wallet:blocks-export:columns-trigger"]')
                ->assertEnabled('[data-testid="wallet:blocks-export:submit"]')
                ->click('[data-testid="wallet:blocks-export:submit"]')
                ->waitForText($successMessage)
                ->assertVisible($downloadSelector)
                ->assertAttribute($downloadSelector, 'download', $downloadName);

            expect($browser->attribute($downloadSelector, 'href'))->toStartWith('data:text/csv');
        });
    })->with('resolutions');
});

describe('Voters Tab', function () {
    beforeEach(function () {
        $this->wallet = Wallet::factory()
            ->activeValidator()
            ->create();
    });

    it('should navigate to tab and back', function ($resolution) {
        $transactions = Transaction::factory()
            ->transfer()
            ->count(5)
            ->create([
                'from'              => $this->wallet->address,
                'to'                => $this->wallet->address,
                'sender_public_key' => $this->wallet->public_key,
            ]);

        $voters = Wallet::factory()
            ->count(10)
            ->create([
                'attributes' => [
                    'vote' => $this->wallet->address,
                ],
            ]);

        $this->browse(function (Browser $browser) use ($transactions, $voters, $resolution) {
            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('wallet', $this->wallet)
                ->waitForText('5 results', ignoreCase: true);

            if ($resolution['width'] < 768) {
                $browser->click('[data-testid="tabs:dropdown:button"]')
                    ->waitForText('Voters')
                    ->clickAtXPath('//div[@data-testid="tabs:dropdown:dropdown"]//span[.//text()="Voters"]');
            } else {
                $browser->click('button#tab-voters');
            }

            $browser->waitForText('10 results', ignoreCase: true);

            foreach ($voters as $voter) {
                if ($resolution['width'] <= 640) {
                    $browser->assertSee(substr($voter->address, 0, 5).'…'.substr($voter->address, -5));
                } else {
                    $browser->assertSee(substr($voter->address, 0, 7));
                }
            }

            if ($resolution['width'] < 768) {
                $browser->click('[data-testid="tabs:dropdown:button"]')
                    ->waitForText('Transactions')
                    ->clickAtXPath('//div[@data-testid="tabs:dropdown:dropdown"]//span[.//text()="Transactions"]');
            } else {
                $browser->click('button#tab-transactions');
            }

            $browser->waitForText('5 results', ignoreCase: true);

            foreach ($transactions as $transaction) {
                $browser->assertSee(substr($transaction->hash, 0, 5));
            }
        });
    })->with('resolutions');

    it('should show tab on page load from query string', function ($resolution) {
        $transactions = Transaction::factory()
            ->transfer()
            ->count(5)
            ->create([
                'from'              => $this->wallet->address,
                'to'                => $this->wallet->address,
                'sender_public_key' => $this->wallet->public_key,
            ]);

        $voters = Wallet::factory()
            ->count(10)
            ->create([
                'attributes' => [
                    'vote' => $this->wallet->address,
                ],
            ]);

        $this->browse(function (Browser $browser) use ($transactions, $voters, $resolution) {
            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('wallet', ['wallet' => $this->wallet, 'tab' => 'voters'])
                ->waitForText('10 results', ignoreCase: true);

            foreach ($voters as $voter) {
                if ($resolution['width'] <= 640) {
                    $browser->assertSee(substr($voter->address, 0, 5).'…'.substr($voter->address, -5));
                } else {
                    $browser->assertSee(substr($voter->address, 0, 7));
                }
            }

            if ($resolution['width'] < 768) {
                $browser->click('[data-testid="tabs:dropdown:button"]')
                    ->waitForText('Transactions')
                    ->clickAtXPath('//div[@data-testid="tabs:dropdown:dropdown"]//span[.//text()="Transactions"]');
            } else {
                $browser->click('button#tab-transactions');
            }

            $browser->waitForText('5 results', ignoreCase: true);

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
