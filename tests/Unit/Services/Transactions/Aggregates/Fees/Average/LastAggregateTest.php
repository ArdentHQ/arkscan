<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Services\Timestamp;
use App\Services\Transactions\Aggregates\Fees\Average\LastAggregate;
use Carbon\Carbon;
use function Tests\configureExplorerDatabase;

beforeEach(fn () => configureExplorerDatabase());

it('should determine the average fee', function ($limit, $type) {
    Carbon::setTestNow('2021-01-01 00:00:00');

    $state = $type === 'magistrate' ? 'entityResignation' : $type;
    $count = ($limit + 5) / 3;
    Transaction::factory($count)->$state()->create(['fee' => '12345678910', 'timestamp' => Timestamp::now()->unix()]);
    Transaction::factory($count)->$state()->create(['fee' => '2345678910', 'timestamp' => Timestamp::now()->unix()]);
    Transaction::factory($count)->$state()->create(['fee' => '345678910', 'timestamp' => Timestamp::now()->unix()]);
    Transaction::factory($count)->create(['fee' => '12345678910', 'timestamp' => Timestamp::now()->unix()]);
    Transaction::factory($count)->create(['fee' => '2345678910', 'timestamp' => Timestamp::now()->unix()]);
    Transaction::factory($count)->create(['fee' => '345678910', 'timestamp' => Timestamp::now()->unix()]);

    $result = (new LastAggregate())->setLimit($limit)->setType($type)->aggregate();

    expect($result)->toBeFloat();
    expect($result)->toBe(59.4567891);
})->with([
    [20, 'delegateRegistration'],
    [20, 'delegateResignation'],
    [20, 'ipfs'],
    [20, 'multiPayment'],
    [20, 'multiSignature'],
    [20, 'secondSignature'],
    [20, 'timelockClaim'],
    [20, 'timelockRefund'],
    [20, 'timelock'],
    [20, 'transfer'],
    [20, 'vote'],
    [20, 'voteCombination'],
    [20, 'magistrate'],
]);
