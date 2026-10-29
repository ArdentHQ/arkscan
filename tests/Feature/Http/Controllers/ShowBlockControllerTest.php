<?php

declare(strict_types=1);

use App\Models\Block;
use function Tests\fakeArkPricing;

it('should render the page without any errors', function () {
    $this->withoutExceptionHandling();

    fakeArkPricing();

    $this
        ->get(route('block', Block::factory()->create()))
        ->assertOk();
});
