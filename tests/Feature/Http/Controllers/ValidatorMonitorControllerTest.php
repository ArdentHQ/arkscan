<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Models\Block;
use App\Models\Wallet;
use App\Services\Cache\WalletCache;

use function Tests\createRoundEntry;

it('should render the page without any errors', function () {
    $wallets = Wallet::factory(Network::validatorCount())->create();

    createRoundEntry(1, 1, $wallets);

    $wallets->each(function ($wallet) {
        for ($i = 0; $i < 3; $i++) {
            Block::factory()->create([
                'number'            => $i,
                'proposer'          => $wallet->address,
            ]);
        }

        (new WalletCache())->setValidator($wallet->address, $wallet);
    });

    $this
        ->get(route('validator-monitor'))
        ->assertOk();
});
