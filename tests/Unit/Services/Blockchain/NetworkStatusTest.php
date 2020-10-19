<?php

declare(strict_types=1);

use App\Models\Block;

use App\Services\Blockchain\NetworkStatus;
use function Tests\configureExplorerDatabase;

it('should get the height', function () {
    configureExplorerDatabase();

    Block::factory()->create(['height' => 5651290]);

    expect(NetworkStatus::height())->toBe(5651290);
});
