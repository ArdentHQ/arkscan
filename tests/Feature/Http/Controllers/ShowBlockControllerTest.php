<?php

declare(strict_types=1);

use App\Models\Block;

use function Tests\configureExplorerDatabase;
use function Tests\fakeCryptoCompare;

it('should render the page without any errors', function () {
    $this->withoutExceptionHandling();

    fakeCryptoCompare();

    configureExplorerDatabase();

    $this
        ->get(route('block', Block::factory()->create()))
        ->assertOk();
});
