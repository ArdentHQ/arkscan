<?php

declare(strict_types=1);

use App\Models\Round;
use App\ViewModels\RoundViewModel;
use function Spatie\Snapshots\assertMatchesSnapshot;

beforeEach(function () {
    $this->subject = new RoundViewModel(Round::factory()->create(['balance' => '500000000000']));
});

it('should get the balance', function () {
    expect($this->subject->balance())->toBeFloat();

    assertMatchesSnapshot($this->subject->balance());
});
