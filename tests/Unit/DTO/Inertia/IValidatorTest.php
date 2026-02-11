<?php

declare(strict_types=1);

use App\Contracts\Network as NetworkContract;
use App\DTO\Inertia\IValidator;
use App\Facades\Network;
use App\Models\Wallet;
use App\Services\ArkVaultUrlBuilder;
use App\Services\BigNumber;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\WalletCache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    Cache::flush();
    mockNetwork();
});

it('should make an instance for active validators', function () {
    $wallet = Wallet::factory()
        ->activeValidator()
        ->make([
            'attributes'    => [
                'validatorPublicKey'   => 'public-key',
                'validatorRank'        => 5,
                'validatorVoteBalance' => 200 * 1e18,
            ],
            'missed_blocks' => 3,
        ]);

    (new NetworkCache())->setSupply(fn () => 10000 * 1e18);
    (new WalletCache())->setProductivity($wallet->address, 99.9);
    (new WalletCache())->setVoterCount($wallet->address, 123);

    $subject = IValidator::fromModel($wallet);

    expect($subject->toArray())->toEqual([
        'rank'              => 5,
        'address'           => $wallet->address,
        'isActive'          => true,
        'isDormant'         => false,
        'isResigned'        => false,
        'username'          => null,
        'hasUsername'       => false,
        'voterCount'        => 123,
        'votes'             => 200.0,
        'votesPercentage'   => 2.0,
        'missedBlocks'      => 3,
        'missedBlocksState' => 'success',
        'voteUrl'           => ArkVaultUrlBuilder::get()->generateVote($wallet->public_key),
    ]);
});

it('should flag warning state when productivity is below warning threshold', function () {
    $wallet = Wallet::factory()
        ->activeValidator()
        ->make([
            'attributes'    => [
                'validatorPublicKey'   => 'public-key',
                'validatorRank'        => 6,
                'validatorVoteBalance' => 100 * 1e18,
            ],
            'missed_blocks' => 1,
        ]);

    (new NetworkCache())->setSupply(fn () => 10000 * 1e18);
    (new WalletCache())->setProductivity($wallet->address, 99.0);

    $subject = IValidator::fromModel($wallet);

    expect($subject->missedBlocksState)->toBe('warning');
});

it('should flag danger state when productivity is below danger threshold', function () {
    $wallet = Wallet::factory()
        ->activeValidator()
        ->make([
            'attributes'    => [
                'validatorPublicKey'   => 'public-key',
                'validatorRank'        => 7,
                'validatorVoteBalance' => 50 * 1e18,
            ],
        ]);

    (new NetworkCache())->setSupply(fn () => 10000 * 1e18);
    (new WalletCache())->setProductivity($wallet->address, 50.0);

    $subject = IValidator::fromModel($wallet);

    expect($subject->missedBlocksState)->toBe('danger');
});

it('should set inactive state for non-active validators', function () {
    $wallet = Wallet::factory()
        ->standbyValidator()
        ->make([
            'attributes'    => [
                'validatorPublicKey'   => 'public-key',
                'validatorRank'        => 60,
                'validatorVoteBalance' => 75 * 1e18,
            ],
            'missed_blocks' => 0,
        ]);

    (new NetworkCache())->setSupply(fn () => 10000 * 1e18);
    (new WalletCache())->setVoterCount($wallet->address, 10);

    $subject = IValidator::fromModel($wallet);

    expect($subject->isActive)->toBeFalse();
    expect($subject->missedBlocksState)->toBe('inactive');
});

it('should not build a vote url for non-validators', function () {
    $wallet = Wallet::factory()->make([
        'public_key' => 'public-key',
        'attributes' => [],
    ]);

    $subject = IValidator::fromModel($wallet);

    expect($subject->voteUrl)->toBeNull();
});

function mockNetwork(float $supply = 10000 * 1e18): void
{
    $mock = Mockery::mock(NetworkContract::class);

    $mock->shouldReceive('coin')->andReturn('DARK');
    $mock->shouldReceive('name')->andReturn('ARK');
    $mock->shouldReceive('alias')->andReturn('devnet');
    $mock->shouldReceive('api')->andReturn('https://example.test');
    $mock->shouldReceive('explorerTitle')->andReturn('Explorer');
    $mock->shouldReceive('currency')->andReturn('DARK');
    $mock->shouldReceive('currencySymbol')->andReturn('DѦ');
    $mock->shouldReceive('confirmations')->andReturn(51);
    $mock->shouldReceive('knownWalletsUrl')->andReturn(null);
    $mock->shouldReceive('knownWallets')->andReturn([]);
    $mock->shouldReceive('knownContracts')->andReturn([]);
    $mock->shouldReceive('knownContract')->andReturn(null);
    $mock->shouldReceive('contractMethod')->andReturn('');
    $mock->shouldReceive('canBeExchanged')->andReturn(false);
    $mock->shouldReceive('epoch')->andReturn(Carbon::now());
    $mock->shouldReceive('validatorCount')->andReturn(53);
    $mock->shouldReceive('blockTime')->andReturn(8);
    $mock->shouldReceive('blockReward')->andReturn(2);
    $mock->shouldReceive('supply')->andReturn(BigNumber::new($supply));
    $mock->shouldReceive('config')->andReturn(Mockery::mock(BitWasp\Bitcoin\Network\Network::class));
    $mock->shouldReceive('toArray')->andReturn([]);
    $mock->shouldReceive('nethash')->andReturn('nethash');

    Network::swap($mock);
}
