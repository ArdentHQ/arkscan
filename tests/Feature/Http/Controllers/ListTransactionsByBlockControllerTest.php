<?php

declare(strict_types=1);

use App\Models\Block;

use function Tests\configureExplorerDatabase;

it('should render the page without any errors', function () {
    configureExplorerDatabase();

    $this
        ->get(route('block.transactions', Block::factory()->create()))
        ->assertNoContent();
});
