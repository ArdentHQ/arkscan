<?php

declare(strict_types=1);

use App\Models\Round;
use App\ViewModels\RoundViewModel;

use function Tests\configureExplorerDatabase;

beforeEach(function () {
    configureExplorerDatabase();

    $this->subject = new RoundViewModel(Round::factory()->create(['balance' => 5000 * 1e8]));
});

it('should get the balance', function () {
    expect($this->subject->balance())->toBeString();
    expect($this->subject->balance())->toBe('ARK 5,000.00');
});
