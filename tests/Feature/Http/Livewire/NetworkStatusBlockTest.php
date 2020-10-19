<?php

declare(strict_types=1);

use App\Http\Livewire\NetworkStatusBlock;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

it('should render with a height, name, supply and market cap', function () {
    $blockchainStatus = [
        'data' => [
            'block' => [
                'height' => 5651290,
                'id'     => '7454506361e241a5c2c5d930fb059d28e3686a7aedc8058d9aac02f70aefe101',
            ],
            'supply' => '13628098200000000',
        ],
    ];

    Http::fakeSequence()
        ->push($blockchainStatus)
        ->push(['USD' => 0.2907])
        ->push($blockchainStatus);

    Livewire::test(NetworkStatusBlock::class)
        ->assertSee('Height: 5,651,290')
        ->assertSee('Network: ARK Public Network')
        ->assertSee('Supply: Ѧ 136,280,982')
        ->assertSee('Market Cap: $39,616,881.47');
});
