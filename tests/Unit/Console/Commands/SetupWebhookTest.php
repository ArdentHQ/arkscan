<?php

declare(strict_types=1);

use App\Console\Commands\CacheFees;
use App\Console\Commands\SetupWebhook;
use App\Models\Transaction;
use App\Services\Cache\FeeCache;
use App\Services\Timestamp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Console\Output\OutputInterface;

it('should execute the command', function () {
    Http::fake(Http::response([
        'data' => [
            'id'    => 'responseId',
            'token' => 'random-token',
        ],
    ], 200));

    Artisan::call('ark:webhook:setup', [
        '--host' => '1.2.3.4',
    ]);

    $output = Artisan::output();

    expect($output)->toContain('ID: responseId');
    expect($output)->toContain('Token: random-token');
});
