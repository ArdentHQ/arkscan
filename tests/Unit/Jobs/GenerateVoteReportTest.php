<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Jobs\GenerateVoteReport;
use Illuminate\Support\Facades\Http;

it('generates vote report file', function () {
    $validatorCount = Network::validatorCount();

    Http::fake([
        "*/validators?page=1&limit={$validatorCount}" => Http::response([
            'data' => [
                [
                    'address'    => '0xe5a97E663158dEaF3b65bBF88897b8359Dc19F81',
                    'publicKey'  => 'pubkey1',
                    'attributes' => [
                        'username'             => 'genesis_1',
                        'validatorRank'        => 1,
                        'validatorApproval'    => 15.50,
                        'validatorVoteBalance' => '30000000000000000000000',
                        'validatorVotersCount' => 150,
                    ],
                ],
                [
                    'address'    => '0xD6Z26L69gdk9qYmTv5uzk3uGepigtHY4ax',
                    'publicKey'  => 'pubkey2',
                    'attributes' => [
                        'validatorRank'        => 2,
                        'validatorApproval'    => 10.25,
                        'validatorVoteBalance' => '20000000000000000000000',
                        'validatorVotersCount' => 75,
                    ],
                ],
            ],
        ]),
        "*/validators?page=2&limit={$validatorCount}" => Http::response([
            'data' => [],
        ]),
        '*/blockchain' => Http::response([
            'data' => ['supply' => '100000000000000000000000'],
        ]),
    ]);

    (new GenerateVoteReport())->handle();

    $outputPath = public_path('VoteReport.txt');
    expect(file_exists($outputPath))->toBeTrue();

    $content = file_get_contents($outputPath);
    expect($content)->toContain(sprintf('Top %d Validators Stats', $validatorCount));
    expect($content)->toContain('Total Votes');
    expect($content)->toContain('Total Voters : 225');
    expect($content)->toContain('genesis_1');
    expect($content)->toContain('0xD6Z26L69gdk9qYmTv5uzk3uGepigtHY4ax');
    expect($content)->toContain(sprintf('%d active validators only', $validatorCount));

    @unlink($outputPath);
});

it('includes second page validators in the report', function () {
    $validatorCount = Network::validatorCount();

    Http::fake([
        "*/validators?page=1&limit={$validatorCount}" => Http::response([
            'data' => [
                [
                    'address'    => '0xActiveValidator1',
                    'publicKey'  => 'pubkey1',
                    'attributes' => [
                        'username'             => 'active_delegate',
                        'validatorRank'        => 1,
                        'validatorApproval'    => 10.00,
                        'validatorVoteBalance' => '10000000000000000000000',
                        'validatorVotersCount' => 50,
                    ],
                ],
            ],
        ]),
        "*/validators?page=2&limit={$validatorCount}" => Http::response([
            'data' => [
                [
                    'address'    => '0xStandbyValidator1',
                    'publicKey'  => 'pubkey2',
                    'attributes' => [
                        'validatorRank'        => 54,
                        'validatorApproval'    => 0.01,
                        'validatorVoteBalance' => '1000000000000000000000',
                        'validatorVotersCount' => 5,
                    ],
                ],
            ],
        ]),
        '*/blockchain' => Http::response([
            'data' => ['supply' => '100000000000000000000000'],
        ]),
    ]);

    (new GenerateVoteReport())->handle();

    $outputPath = public_path('VoteReport.txt');
    $content    = file_get_contents($outputPath);

    expect($content)->toContain('active_delegate');
    expect($content)->toContain('0xStandbyValidator1');
    expect($content)->toContain('Total Voters : 50');

    @unlink($outputPath);
});

it('handles null api responses gracefully', function () {
    Http::fake([
        '*' => Http::response(null, 500),
    ]);

    (new GenerateVoteReport())->handle();

    expect(file_exists(public_path('VoteReport.txt')))->toBeFalse();
});
