<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;

it('redirects to the correct route', function ($model, $oldUrl, $newUrl): void {
    $modelId = $model::factory()->create()->id;

    $this->get(sprintf('%s/%s', $oldUrl, $modelId))->assertRedirect(sprintf('%s/%s', $newUrl, $modelId));
})->with([
    [Transaction::class, 'transaction', 'transactions'],
    [Wallet::class, 'wallet', 'wallets'],
    [Block::class, 'block', 'blocks'],
]);
