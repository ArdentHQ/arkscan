<?php

declare(strict_types=1);

use App\Models\Wallet;

use function Tests\configureExplorerDatabase;

it('should render the page without any errors', function () {
    $this
        ->get(route('home'))
        ->assertOk();
});
