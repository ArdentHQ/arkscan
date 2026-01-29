<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\Wallet;
use App\Services\Cache\WalletCache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Laravel\Dusk\Browser;

beforeEach(function () {
    $this->withoutExceptionHandling();
});

afterEach(function () {
    Cache::flush();
});

it('should display block details', function ($resolution) {
    $validator = Wallet::factory()
        ->activeValidator()
        ->create([
            'attributes' => [
                'username' => 'test-validator',
            ],
        ]);

    (new WalletCache())->setWalletNameByAddress($validator->address, 'test-validator');

    $block = Block::factory()->create([
        'number'             => 123456,
        'timestamp'          => Carbon::now()->subHours(2)->getTimestampMs(),
        'transactions_count' => 42,
        'reward'             => 3.9 * 1e18,
        'fee'                => 0.5 * 1e18,
        'proposer'           => $validator->address,
    ]);

    $this->browse(function (Browser $browser) use ($block, $resolution) {
        $browser->resize($resolution['width'], $resolution['height']);

        $browser->visitRoute('block', $block)
            ->waitForText(substr($block->hash, 0, 7))
            ->assertSee('123,456')
            ->assertSee('42')
            ->assertSee('test-validator')
            ->assertSee('3.90 DARK')
            ->assertSee('0.50 DARK');
    });
})->with('resolutions');

it('should copy block id to clipboard', function ($resolution) {
    $validator = Wallet::factory()->create();

    $block = Block::factory()->create([
        'proposer' => $validator->address,
    ]);

    $this->browse(function (Browser $browser) use ($block, $resolution) {
        $this->grantPermission($browser, ['clipboardReadWrite', 'clipboardSanitizedWrite']);

        $browser->resize($resolution['width'], $resolution['height']);

        $browser->visitRoute('block', $block)
            ->waitForText(substr($block->hash, 0, 7))
            ->click('[data-testid="block:copy-id"] button')
            ->waitForText(trans('pages.block.block_id_copied'));

        $browser->assertScript('navigator.clipboard.readText()', $block->hash);
    });
})->with('resolutions');

it('should display validator address when no username', function ($resolution) {
    $validator = Wallet::factory()->create();

    $block = Block::factory()->create([
        'proposer' => $validator->address,
    ]);

    $this->browse(function (Browser $browser) use ($block, $validator, $resolution) {
        $browser->resize($resolution['width'], $resolution['height']);

        $browser->visitRoute('block', $block)
            ->waitForText(substr($block->hash, 0, 7));

        // On mobile (<768px) address is truncated in middle, on desktop it's shown in full
        if ($resolution['width'] >= 768) {
            $browser->assertSee(substr($validator->address, 0, 10));
        } else {
            // TruncateMiddle shows: 0xB32...0778c format
            $browser->assertSee(substr($validator->address, 0, 5).'…'.substr($validator->address, -5));
        }
    });
})->with('resolutions');

it('should not display transactions section when block has no transactions', function ($resolution) {
    $validator = Wallet::factory()->create();

    $block = Block::factory()->create([
        'transactions_count' => 0,
        'proposer'           => $validator->address,
    ]);

    $this->browse(function (Browser $browser) use ($block, $resolution) {
        $browser->resize($resolution['width'], $resolution['height']);

        $browser->visitRoute('block', $block)
            ->waitForText(substr($block->hash, 0, 7))
            ->assertSee('0')
            ->assertDontSee('Tx ID');
    });
})->with('resolutions');

it('should navigate to validator wallet when clicking validator link', function ($resolution) {
    $validator = Wallet::factory()->create();

    (new WalletCache())->setWalletNameByAddress($validator->address, 'click-validator');

    $block = Block::factory()->create([
        'proposer' => $validator->address,
    ]);

    $this->browse(function (Browser $browser) use ($block, $validator, $resolution) {
        $browser->resize($resolution['width'], $resolution['height']);

        $browser->visitRoute('block', $block)
            ->waitForText(substr($block->hash, 0, 7))
            ->clickLink('click-validator')
            ->waitForText(substr($validator->address, 0, 7))
            ->assertPathIs('/addresses/'.$validator->address);
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
