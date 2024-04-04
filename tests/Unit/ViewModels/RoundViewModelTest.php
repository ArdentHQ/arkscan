<?php

declare(strict_types=1);

use App\Models\Round;
use App\ViewModels\RoundViewModel;

it('should instantiate', function () {
    $round = Round::factory()->create();
    $viewModel = new RoundViewModel($round);

    expect($viewModel->model())->toBe($round);
});
