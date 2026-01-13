<?php

declare(strict_types=1);

use App\Models\Block;
use function Tests\fakeCryptoCompare;

it('should render the page without any errors', function () {
    $this->withoutExceptionHandling();

    fakeCryptoCompare();

    $this
        ->get(route('old-block', Block::factory()->create()->hash))
        ->assertOk();
});
