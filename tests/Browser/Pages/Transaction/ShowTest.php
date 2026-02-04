<?php

declare(strict_types=1);

use App\Console\Commands\CacheTokens;
use App\Facades\Network;
use App\Models\Block;
use App\Models\MultiPayment;
use App\Models\Token;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BigNumber;
use App\Services\Cache\NetworkCache;
use Carbon\Carbon;
use Facebook\WebDriver\WebDriverBy;
use Laravel\Dusk\Browser;

beforeEach(function () {
    $this->wallet          = Wallet::factory()->create(['attributes' => []]);
    $this->recipientWallet = Wallet::factory()->create(['attributes' => []]);
});

// A duplication of safeUtf8 from App\DTO\Inertia\TransactionDetails to normalize invalid UTF-8 payloads
function safeUtf8(string $value): string
{
    // Skip normalization when the payload is already valid UTF-8.
    if (preg_match('//u', $value) === 1) {
        return $value;
    }

    // Invalid UTF-8 in payloads breaks JSON encoding and causes Inertia JSON.parse errors;
    // normalize to the replacement character to keep the response valid.
    $previousSubstitute = mb_substitute_character();
    mb_substitute_character(0xFFFD);
    $converted = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
    mb_substitute_character($previousSubstitute);

    return $converted;
}

it('should show basic transaction details', function ($resolution) {
    $transaction = Transaction::factory()
        ->transfer()
        ->create([
            'block_hash'        => Block::factory()->create(['number' => 432])->hash,
            'from'              => $this->wallet->address,
            'to'                => $this->recipientWallet->address,
            'sender_public_key' => $this->wallet->public_key,
            'value'             => 123.45 * 1e18,
            'gas'               => 12000,
            'gas_price'         => 1e9,
            'gas_used'          => 21000,
            'gas_refunded'      => 10000,
            'status'            => true,
            'block_number'      => 12,
        ]);

    (new NetworkCache())->setHeight(fn (): int => 432);

    $this->browse(function (Browser $browser) use ($transaction, $resolution) {
        $browser->resize($resolution['width'], $resolution['height']);

        $transactionIdPart1 = substr($transaction->hash, 0, 6);
        $transactionIdPart2 = substr($transaction->hash, 6, 6);

        $senderAddress = $resolution['width'] < 768
            ? substr($this->wallet->address, 0, 5).'…'.substr($this->wallet->address, -5)
            : $this->wallet->address;

        $recipientAddress = $resolution['width'] < 768
            ? substr($this->recipientWallet->address, 0, 5).'…'.substr($this->recipientWallet->address, -5)
            : $this->recipientWallet->address;

        $browser->visit('/transactions/'.$transaction->hash)
            ->waitForText($transactionIdPart1)
            ->assertSee($transactionIdPart2)
            ->assertSeeInOrder([
                'Timestamp',
                Carbon::createFromTimestamp($transaction->timestamp)->format('d M Y H:i:s'),
                'Block',
                number_format($transaction->block_number),
                'Nonce',
                $transaction->nonce,
                'Action',
                'Method',
                'Transfer',
                'Addressing',
                'From',
                $senderAddress,
                'To',
                $recipientAddress,
                'Transaction Summary',
                'Amount',
                '123.45 DARK',
                'Fee',
                '0.000021 DARK',
                'Status',
                'Success',
                (432 - 12).' Confirmations',
                'More Details',
                'Gas Information',
                'Gas Limit',
                '12,000',
                'Usage By Txn',
                '21,000',
                'Other Attributes',
                'Position In Block',
                $transaction->transaction_index,
            ]);
    });
})->with('resolutions');

it('should show input data', function ($resolution) {
    $blsPublicKey = 'ef41bc3a8f1dfe662847604c04aa5e5e80c23df2ed657d687149b964a8a8f3a0';

    $transaction = Transaction::factory()
        ->validatorRegistration($blsPublicKey)
        ->create([
            'from'              => $this->wallet->address,
            'sender_public_key' => $this->wallet->public_key,
            'value'             => 123.45 * 1e18,
            'gas'               => 12000,
            'gas_price'         => 1e9,
            'gas_used'          => 21000,
            'gas_refunded'      => 10000,
            'status'            => true,
            'block_number'      => 12,
        ]);

    (new NetworkCache())->setHeight(fn (): int => 432);

    $this->browse(function (Browser $browser) use ($transaction, $resolution, $blsPublicKey) {
        $browser->resize($resolution['width'], $resolution['height']);

        $transactionIdPart1 = substr($transaction->hash, 0, 6);
        $transactionIdPart2 = substr($transaction->hash, 6, 6);

        $senderAddress = $resolution['width'] < 768
            ? substr($this->wallet->address, 0, 5).'…'.substr($this->wallet->address, -5)
            : $this->wallet->address;

        $recipientAddress = $resolution['width'] < 768 ? '' : $transaction->to;

        $browser->visit('/transactions/'.$transaction->hash)
            ->waitForText($transactionIdPart1)
            ->assertSee($transactionIdPart2)
            ->assertSeeInOrder([
                'Nonce',
                $transaction->nonce,
                'Action',
                'Method',
                'Validator Registration',
                'Addressing',
                'From',
                $senderAddress,
                'Interacted With',
                $recipientAddress,
                'Contract',
                'Transaction Summary',
            ]);

        $viewAllButtons = $browser->driver->findElements(WebDriverBy::xpath('//button[text()="View All"]'));
        foreach ($viewAllButtons as $button) {
            if (! $button->isDisplayed()) {
                continue;
            }

            $button->click();
        }

        $browser->assertSeeInOrder([
            'Transaction Summary',
            'Input Data',
            'Function: registerValidator(bytes)',
            'MethodID: 0x'.Network::contractMethod('validator_registration', '602a9eee'),
            '[0]: '.$blsPublicKey,
        ]);

        $utf8Buttons = $browser->driver->findElements(WebDriverBy::xpath('//button[.//div[text()="UTF-8"]]'));
        foreach ($utf8Buttons as $button) {
            if (! $button->isDisplayed()) {
                continue;
            }

            $button->click();
        }

        $browser->assertSeeInOrder([
            'Transaction Summary',
            'Input Data',
            safeUtf8($transaction->utf8Payload()),
        ]);

        $originalButtons = $browser->driver->findElements(WebDriverBy::xpath('//button[.//div[text()="Original"]]'));
        foreach ($originalButtons as $button) {
            if (! $button->isDisplayed()) {
                continue;
            }

            $button->click();
        }

        $browser->assertSeeInOrder([
            'Transaction Summary',
            'Input Data',
            $transaction->rawPayload(),
        ]);
    });
})->with('resolutions');

it('should show multipayment recipients', function ($resolution) {
    $recipient1 = Wallet::factory()->create();
    $recipient2 = Wallet::factory()->create();

    $recipients = [$recipient1, $recipient2];
    $amounts    = [
        BigNumber::new('10000000000000000000'),
        BigNumber::new('1000000000000000000'),
    ];

    $transaction = Transaction::factory()
        ->multiPayment([
            $recipients[0]->address,
            $recipients[1]->address,
        ], [
            $amounts[0],
            $amounts[1],
        ])
        ->create([
            'timestamp' => Carbon::parse('2017-03-21 13:00:00')->getTimestampMs(),
            'value'     => 11 * 1e18,
            'gas_price' => 100000000,
        ]);

    foreach (range(0, 1) as $index) {
        MultiPayment::factory()->create([
            'hash'      => $transaction->hash,
            'to'        => $recipients[$index]->address,
            'amount'    => (string) $amounts[$index],
            'log_index' => 1,
        ]);
    }

    (new NetworkCache())->setHeight(fn (): int => 432);

    $this->browse(function (Browser $browser) use ($transaction, $recipient1, $recipient2, $resolution) {
        $this->grantPermission($browser, ['clipboardReadWrite', 'clipboardSanitizedWrite']);

        $browser->resize($resolution['width'], $resolution['height']);

        $transactionIdPart1 = substr($transaction->hash, 0, 6);
        $transactionIdPart2 = substr($transaction->hash, 6, 6);

        $browser->visit('/transactions/'.$transaction->hash)
            ->waitForText($transactionIdPart1)
            ->assertSee($transactionIdPart2)
            ->assertSeeInOrder([
                'Timestamp',
                Carbon::createFromTimestamp($transaction->timestamp)->format('d M Y H:i:s'),
                'Block',
                number_format($transaction->block_number),
                'Nonce',
                $transaction->nonce,
                'Action',
                'Method',
                'Multipayment',
            ]);

        $mappedAddresses = [
            [
                'address' => $recipient1->address,
                'amount'  => '10.00 DARK',
            ],
            [
                'address' => $recipient2->address,
                'amount'  => '1.00 DARK',
            ],
        ];

        foreach ($mappedAddresses as $index => $detail) {
            $displayedAddress = $resolution['width'] < 768
                ? substr($detail['address'], 0, 5).'…'.substr($detail['address'], -5)
                : $detail['address'];

            $browser->assertSeeInOrder([
                $displayedAddress,
                $detail['amount'],
            ]);

            $browser->click('[data-testid="transaction:recipient:'.$index.':address"] button')
                ->waitForText(trans('pages.wallet.address_copied'));

            $browser->assertScript('navigator.clipboard.readText()', $detail['address']);
        }
    });
})->with('resolutions');

it('should copy data to the clipboard', function ($resolution) {
    $blsPublicKey = 'ef41bc3a8f1dfe662847604c04aa5e5e80c23df2ed657d687149b964a8a8f3a0';

    $transaction = Transaction::factory()
        ->validatorRegistration($blsPublicKey)
        ->create([
            'from'              => $this->wallet->address,
            'sender_public_key' => $this->wallet->public_key,
            'value'             => 123.45 * 1e18,
            'gas'               => 12000,
            'gas_price'         => 1e9,
            'gas_used'          => 21000,
            'gas_refunded'      => 10000,
            'status'            => true,
            'block_number'      => 12,
        ]);

    (new NetworkCache())->setHeight(fn (): int => 432);

    $this->browse(function (Browser $browser) use ($transaction, $resolution, $blsPublicKey) {
        $this->grantPermission($browser, ['clipboardReadWrite', 'clipboardSanitizedWrite']);

        $browser->resize($resolution['width'], $resolution['height']);

        $transactionIdPart1 = substr($transaction->hash, 0, 6);

        $browser->visit('/transactions/'.$transaction->hash)
            ->waitForText($transactionIdPart1)
            ->click('[data-testid="transaction:copy-id"] button')
            ->waitForText(trans('pages.transaction.transaction_id_copied'));

        $browser->assertScript('navigator.clipboard.readText()', $transaction->hash);

        $browser->click('[data-testid="transaction:copy-from:address"] button')
            ->waitForText(trans('pages.wallet.address_copied'));

        $browser->assertScript('navigator.clipboard.readText()', $this->wallet->address);

        $browser->click('[data-testid="transaction:copy-to:address"] button')
            ->waitForText(trans('pages.wallet.address_copied'));

        $browser->assertScript('navigator.clipboard.readText()', $transaction->to);
    });
})->with('resolutions');

it('should show token transfer symbol', function ($resolution) {
    $contractWallet = Wallet::factory()->create(['attributes' => []]);
    $transaction    = Transaction::factory()
        ->tokenTransfer($this->recipientWallet->address, BigNumber::new(1234.56 * 1e18))
        ->create([
            'from'              => $this->wallet->address,
            'sender_public_key' => $this->wallet->public_key,
            'to'                => $contractWallet->address,
            'value'             => 123.45 * 1e18,
            'gas'               => 12000,
            'gas_price'         => 1e9,
            'gas_used'          => 21000,
            'gas_refunded'      => 10000,
        ]);

    Token::factory()->create([
        'address' => $transaction->to,
        'symbol'  => 'TESTINGSYMBOL',
    ]);

    (new CacheTokens())->handle();

    (new NetworkCache())->setHeight(fn (): int => 432);

    $this->browse(function (Browser $browser) use ($transaction, $resolution, $contractWallet) {
        $browser->resize($resolution['width'], $resolution['height']);

        $transactionIdPart1 = substr($transaction->hash, 0, 6);
        $transactionIdPart2 = substr($transaction->hash, 6, 6);

        $senderAddress = $resolution['width'] < 768
            ? substr($this->wallet->address, 0, 5).'…'.substr($this->wallet->address, -5)
            : $this->wallet->address;

        $recipientAddress = $resolution['width'] < 768
            ? substr($this->recipientWallet->address, 0, 5).'…'.substr($this->recipientWallet->address, -5)
            : $this->recipientWallet->address;

        $contractAddress = $resolution['width'] < 768 ? 'Contract' : $contractWallet->address;

        $browser->visit('/transactions/'.$transaction->hash)
            ->waitForText($transactionIdPart1)
            ->assertSee($transactionIdPart2)
            ->assertSeeInOrder([
                'Addressing',
                'From',
                $senderAddress,
                'Interacted With',
                $contractAddress,
                'Tokens Transferred',
                'To',
                $recipientAddress,
                'Amount',
                '1234.56 TESTINGSYMBOL',
                'Transaction Summary',
                'Amount',
                '123.45 DARK',
                'Fee',
                '0.000021 DARK',
            ]);
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
