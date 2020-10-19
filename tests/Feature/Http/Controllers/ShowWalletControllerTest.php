<?php

declare(strict_types=1);

use App\Models\Wallet;

use function Tests\configureExplorerDatabase;

it('should render the page without any errors', function () {
    configureExplorerDatabase();

    $this
        ->get(route('wallet', Wallet::factory()->create()))
        ->assertNoContent();
});
