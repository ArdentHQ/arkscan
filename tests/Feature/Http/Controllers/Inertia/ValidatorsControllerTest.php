<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Http\Controllers\Inertia\ValidatorsController;
use App\Models\ForgingStats;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BigNumber;
use App\Services\Cache\CryptoDataCache;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\NetworkStatusBlockCache;
use App\Services\Cache\ValidatorCache;
use App\Services\Cache\WalletCache;
use App\Services\Timestamp;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Inertia\Testing\AssertableInertia as Assert;

use function Tests\fakeKnownWallets;
use function Tests\faker;

beforeEach(function () {
    $this->withoutExceptionHandling();
});

function performValidatorsRequest($context, $withReload = true, $pageCallback = null, $reloadCallback = null, array $queryString = [], string $reloadProps = 'missedBlocks'): mixed
{
    return $context->get(route('validators', $queryString))
        ->assertOk()
        ->assertInertia(function (Assert $page) use ($pageCallback, $withReload, $reloadCallback, $reloadProps) {
            $page->missing('missedBlocks')
                ->component('Validators/Validators');

            if (is_callable($pageCallback)) {
                $pageCallback($page);
            }

            if (! $withReload) {
                return;
            }

            $page->reloadOnly($reloadProps, function (Assert $reload) use ($reloadCallback) {
                if (is_callable($reloadCallback)) {
                    $reloadCallback($reload);
                }
            });
        });
}

function createValidatorWallet(string $address, int $rank): Wallet
{
    return Wallet::factory()->create([
        'address'    => $address,
        'public_key' => 'public-key-'.$address,
        'attributes' => [
            'username'                => $address,
            'validatorPublicKey'      => 'validator-public-'.$address,
            'validatorRank'           => $rank,
            'validatorResigned'       => false,
            'validatorVoteBalance'    => 10 * 1e8,
            'validatorProducedBlocks' => 100,
            'validatorMissedBlocks'   => 0,
        ],
    ]);
}

function createValidatorWalletWithAttributes(string $address, int $rank, array $attributeOverrides = []): Wallet
{
    return Wallet::factory()->create([
        'address'    => $address,
        'public_key' => 'public-key-'.$address,
        'attributes' => array_merge([
            'username'                => $address,
            'validatorPublicKey'      => 'validator-public-'.$address,
            'validatorRank'           => $rank,
            'validatorResigned'       => false,
            'validatorVoteBalance'    => 10 * 1e8,
            'validatorProducedBlocks' => 100,
            'validatorMissedBlocks'   => 0,
        ], $attributeOverrides),
    ]);
}

it('should render the page without any errors', function () {
    performValidatorsRequest($this);
});

it('should expose filters and statistics data', function () {
    $walletA = Wallet::factory()->create();
    $walletB = Wallet::factory()->create();

    ForgingStats::factory()->create([
        'address' => $walletA->address,
        'forged'  => false,
    ]);

    ForgingStats::factory()->create([
        'address' => $walletA->address,
        'forged'  => false,
    ]);

    ForgingStats::factory()->create([
        'address' => $walletB->address,
        'forged'  => false,
    ]);

    $validatorCache = new ValidatorCache();
    $validatorCache->setTotalWalletsVoted(42);
    $validatorCache->setTotalBalanceVoted(123.45);

    (new NetworkCache())->setVotesPercentage('67.5');

    performValidatorsRequest(
        $this,
        pageCallback: function (Assert $page) {
            $page->where('filters', ValidatorsController::FILTERS)
                ->where('statistics', [
                    'voterCount'       => 42,
                    'totalVoted'       => 123.45,
                    'votesPercentage'  => 67.5,
                    'missedBlocks'     => 3,
                    'validatorsMissed' => 2,
                ]);
        },
        withReload: false,
    );
});

describe('Recent Votes', function () {
    function generateTransactions(): array
    {
        $validator1 = Wallet::factory()->activeValidator()->create([
            'address'    => '0x522CbD1C22529a27ba4BFDBf4b6f037F71b2AC77',
            'attributes' => [
                'username' => 'validator-1',
            ],
        ]);

        $validator2 = Wallet::factory()->activeValidator()->create([
            'address'    => '0x09C94A51cb63b4b70A9Dbf190543c371741D13Fd',
            'attributes' => [
                'username' => 'validator-2',
            ],
        ]);

        $validator3 = Wallet::factory()->activeValidator()->create([
            'address'    => '0x2a74550fC2e741118182B7ab020DC0B7Ed01e1db',
            'attributes' => [
                'username' => 'validator-3',
            ],
        ]);

        $sender1 = Wallet::factory()->create(['address' => '0xC5a19e23E99bdFb7aae4301A009763AdC01c1b5B']);
        $sender2 = Wallet::factory()->create(['address' => '0x38b4a84773bC55e88D07cBFC76444C2A37600084']);

        $voteTransaction = Transaction::factory()
            ->vote($validator1->address)
            ->create([
                'timestamp'      => Carbon::parse('2023-09-18 03:41:04')->getTimestampMs(),
                'from'           => $sender1->address,
                'status'         => true,
            ]);

        $unvoteTransaction = Transaction::factory()
        ->unvote()
        ->create([
            'timestamp'      => Carbon::parse('2023-09-18 04:41:04')->getTimestampMs(),
            'from'           => $sender2->address,
            'status'         => true,
        ]);

        return [
            'validator1'           => $validator1,
            'validator2'           => $validator2,
            'validator3'           => $validator3,
            'voteTransaction'      => $voteTransaction,
            'unvoteTransaction'    => $unvoteTransaction,
        ];
    };

    beforeEach(fn () => $this->travelTo('2023-09-20 05:41:04'));

    it('should provide recent votes data', function () {
        $this->freezeTime();
        $this->travelTo('2025-09-11 12:00:00');

        (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', 2.0);
        (new CryptoDataCache())->setPrices('USD.week', collect([
            Carbon::parse('2025-09-11')->format('Y-m-d') => 2.0,
        ]));

        $walletFrom = Wallet::factory()->create([
            'balance'    => 100.34123 * 1e18,
            'attributes' => [
                'username' => 'joe.blogs',
                'isLegacy' => true,
            ],
        ]);

        $walletTo = Wallet::factory()->activeValidator()->create([
            'balance'    => 50.34123 * 1e18,
            'attributes' => [
                'username' => 'bill.ding',
            ],
        ]);

        $transaction = Transaction::factory()
            ->vote($walletTo->address)
            ->create([
                'sender_public_key' => $walletFrom->public_key,
                'from'              => $walletFrom->address,
                'timestamp'         => Carbon::now()->unix() * 1000,
                'status'            => true,
            ]);

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($transaction, $walletFrom, $walletTo) {
                $reload->has('recentVotes.data', 1)
                    ->where('recentVotes.total', 1)
                    ->where('recentVotes.current_page', 1)
                    ->where('recentVotes.data.0.hash', $transaction->hash)
                    ->where('recentVotes.data.0.votedFor', $walletTo->address)
                    ->where('recentVotes.data.0.sender.address', $walletFrom->address);
            },
            reloadProps: 'recentVotes',
        );
    });

    it('should return no results message when there are no recent votes', function () {
        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) {
                $reload->where('recentVotes.data', [])
                    ->where('recentVotes.total', 0)
                    ->where('recentVotes.noResultsMessage', trans('tables.recent-votes.no_results.no_results'));
            },
            reloadProps: 'recentVotes',
        );
    });

    it('should return no filters message when recent votes filters are disabled', function () {
        $this->freezeTime();
        $this->travelTo('2025-09-11 12:00:00');

        (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', 2.0);
        (new CryptoDataCache())->setPrices('USD.week', collect([
            Carbon::parse('2025-09-11')->format('Y-m-d') => 2.0,
        ]));

        $walletFrom = Wallet::factory()->create([
            'attributes' => [
                'username' => 'recent-voter',
                'isLegacy' => true,
            ],
        ]);

        $walletTo = Wallet::factory()->activeValidator()->create([
            'attributes' => [
                'username' => 'recent-vote-target',
            ],
        ]);

        Transaction::factory()
            ->vote($walletTo->address)
            ->create([
                'sender_public_key' => $walletFrom->public_key,
                'from'              => $walletFrom->address,
                'timestamp'         => Carbon::now()->unix() * 1000,
                'status'            => true,
            ]);

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) {
                $reload->where('recentVotes.data', [])
                    ->where('recentVotes.total', 0)
                    ->where('recentVotes.noResultsMessage', trans('tables.recent-votes.no_results.no_filters'));
            },
            queryString: [
                'vote'   => 0,
                'unvote' => 0,
            ],
            reloadProps: 'recentVotes',
        );
    });

    it('should filter recent votes by vote and unvote flags', function () {
        $this->freezeTime();
        $this->travelTo('2025-09-11 12:00:00');

        (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', 2.0);
        (new CryptoDataCache())->setPrices('USD.week', collect([
            Carbon::parse('2025-09-11')->format('Y-m-d') => 2.0,
        ]));

        $walletFrom = Wallet::factory()->create([
            'attributes' => [
                'username' => 'vote-filter-sender',
                'isLegacy' => true,
            ],
        ]);

        $walletTo = Wallet::factory()->activeValidator()->create([
            'attributes' => [
                'username' => 'vote-filter-target',
            ],
        ]);

        $vote = Transaction::factory()
            ->vote($walletTo->address)
            ->create([
                'sender_public_key' => $walletFrom->public_key,
                'from'              => $walletFrom->address,
                'timestamp'         => Carbon::now()->unix() * 1000,
                'status'            => true,
            ]);

        $unvote = Transaction::factory()
            ->unvote()
            ->create([
                'sender_public_key' => $walletFrom->public_key,
                'from'              => $walletFrom->address,
                'timestamp'         => Carbon::now()->unix() * 1000,
                'status'            => true,
            ]);

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($vote) {
                $reload->has('recentVotes.data', 1)
                    ->where('recentVotes.total', 1)
                    ->where('recentVotes.data.0.hash', $vote->hash);
            },
            queryString: [
                'vote'   => 1,
                'unvote' => 0,
            ],
            reloadProps: 'recentVotes',
        );

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($unvote) {
                $reload->has('recentVotes.data', 1)
                    ->where('recentVotes.total', 1)
                    ->where('recentVotes.data.0.hash', $unvote->hash);
            },
            queryString: [
                'vote'   => 0,
                'unvote' => 1,
            ],
            reloadProps: 'recentVotes',
        );
    });

    it('should sort by age descending by default', function () {
        $data = generateTransactions();

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($data) {
                $reload->has('recentVotes.data', 2)
                    ->where('recentVotes.data.0.hash', $data['unvoteTransaction']->hash)
                    ->where('recentVotes.data.1.hash', $data['voteTransaction']->hash);
            },
            reloadProps: 'recentVotes',
        );
    });

    it('should sort age in ascending order', function () {
        $data = generateTransactions();

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($data) {
                $reload->has('recentVotes.data', 2)
                    ->where('recentVotes.data.0.hash', $data['voteTransaction']->hash)
                    ->where('recentVotes.data.1.hash', $data['unvoteTransaction']->hash);
            },
            queryString: [
                'sort'           => 'age',
                'sort-direction' => 'asc',
            ],
            reloadProps: 'recentVotes',
        );
    });

    it('should sort address in ascending order', function () {
        $data = generateTransactions();

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($data) {
                $reload->has('recentVotes.data', 2)
                    ->where('recentVotes.data.0.hash', $data['unvoteTransaction']->hash)
                    ->where('recentVotes.data.1.hash', $data['voteTransaction']->hash);
            },
            queryString: [
                'sort'           => 'address',
                'sort-direction' => 'asc',
            ],
            reloadProps: 'recentVotes',
        );
    });

    it('should sort address in descending order', function () {
        $data = generateTransactions();

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($data) {
                $reload->has('recentVotes.data', 2)
                    ->where('recentVotes.data.0.hash', $data['voteTransaction']->hash)
                    ->where('recentVotes.data.1.hash', $data['unvoteTransaction']->hash);
            },
            queryString: [
                'sort'           => 'address',
                'sort-direction' => 'desc',
            ],
            reloadProps: 'recentVotes',
        );
    });

    it('should sort type in ascending order', function () {
        $data = generateTransactions();

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($data) {
                $reload->has('recentVotes.data', 2)
                    ->where('recentVotes.data.0.hash', $data['voteTransaction']->hash)
                    ->where('recentVotes.data.1.hash', $data['unvoteTransaction']->hash);
            },
            queryString: [
                'sort'           => 'type',
                'sort-direction' => 'asc',
            ],
            reloadProps: 'recentVotes',
        );
    });

    it('should sort type in descending order', function () {
        $data = generateTransactions();

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($data) {
                $reload->has('recentVotes.data', 2)
                    ->where('recentVotes.data.0.hash', $data['unvoteTransaction']->hash)
                    ->where('recentVotes.data.1.hash', $data['voteTransaction']->hash);
            },
            queryString: [
                'sort'           => 'type',
                'sort-direction' => 'desc',
            ],
            reloadProps: 'recentVotes',
        );
    });

    it('should force default sort direction if invalid query string value', function () {
        $data = generateTransactions();

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($data) {
                $reload->has('recentVotes.data', 2)
                    ->where('recentVotes.data.0.hash', $data['unvoteTransaction']->hash)
                    ->where('recentVotes.data.1.hash', $data['voteTransaction']->hash);
            },
            queryString: [
                'sort'           => 'type',
                'sort-direction' => 'testing',
            ],
            reloadProps: 'recentVotes',
        );
    });

    it('should sort name then address in ascending order when missing names', function () {
        $validator1 = Wallet::factory()->activeValidator()->create([
            'attributes' => [
                'username' => 'validator-name',
            ],
        ]);

        $validator2 = Wallet::factory()->activeValidator()->create([
            'address'    => '0xC5a19e23E99bdFb7aae4301A009763AdC01c1b5B',
            'attributes' => [
                'username' => null,
            ],
        ]);

        $voteTransaction = Transaction::factory()->vote($validator1->address)->create([
            'timestamp' => Carbon::parse('2023-09-18 03:41:04')->getTimestampMs(),
            'status'    => true,
        ]);

        $unvoteTransaction = Transaction::factory()->unvote()->create([
            'timestamp' => Carbon::parse('2023-09-18 04:41:05')->getTimestampMs(),
            'status'    => true,
        ]);

        $voteTransaction2 = Transaction::factory()->vote($validator2->address)->create([
            'timestamp' => Carbon::parse('2023-09-18 05:41:06')->getTimestampMs(),
            'status'    => true,
        ]);

        $unvoteTransaction2 = Transaction::factory()->unvote()->create([
            'timestamp' => Carbon::parse('2023-09-18 06:41:07')->getTimestampMs(),
            'status'    => true,
        ]);

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($voteTransaction, $voteTransaction2, $unvoteTransaction, $unvoteTransaction2) {
                $reload->has('recentVotes.data', 4)
                    ->where('recentVotes.data.0.hash', $voteTransaction2->hash)
                    ->where('recentVotes.data.1.hash', $voteTransaction->hash)
                    ->where('recentVotes.data.2.hash', $unvoteTransaction->hash)
                    ->where('recentVotes.data.3.hash', $unvoteTransaction2->hash);
            },
            queryString: [
                'sort'           => 'name',
                'sort-direction' => 'asc',
            ],
            reloadProps: 'recentVotes',
        );
    });

    it('should sort name then address in descending order when missing names', function () {
        $validator1 = Wallet::factory()->activeValidator()->create([
            'attributes' => [
                'username' => 'validator-name',
            ],
        ]);

        $validator2 = Wallet::factory()->activeValidator()->create([
            'address'    => '0xC5a19e23E99bdFb7aae4301A009763AdC01c1b5B',
            'attributes' => [
                'username' => null,
            ],
        ]);

        $voteTransaction = Transaction::factory()->vote($validator1->address)->create([
            'timestamp' => Carbon::parse('2023-09-18 03:41:04')->getTimestampMs(),
            'status'    => true,
        ]);

        $unvoteTransaction = Transaction::factory()->unvote()->create([
            'timestamp' => Carbon::parse('2023-09-18 04:41:05')->getTimestampMs(),
            'status'    => true,
        ]);

        $voteTransaction2 = Transaction::factory()->vote($validator2->address)->create([
            'timestamp' => Carbon::parse('2023-09-18 05:41:06')->getTimestampMs(),
            'status'    => true,
        ]);

        $unvoteTransaction2 = Transaction::factory()->unvote()->create([
            'timestamp' => Carbon::parse('2023-09-18 06:41:07')->getTimestampMs(),
            'status'    => true,
        ]);

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($voteTransaction, $voteTransaction2, $unvoteTransaction, $unvoteTransaction2) {
                $reload->has('recentVotes.data', 4)
                    ->where('recentVotes.data.0.hash', $voteTransaction->hash)
                    ->where('recentVotes.data.1.hash', $voteTransaction2->hash)
                    ->where('recentVotes.data.2.hash', $unvoteTransaction->hash)
                    ->where('recentVotes.data.3.hash', $unvoteTransaction2->hash);
            },
            queryString: [
                'sort'           => 'name',
                'sort-direction' => 'desc',
            ],
            reloadProps: 'recentVotes',
        );
    });

    it('should sort known name, then name, then address in ascending order when missing names', function () {
        fakeKnownWallets();

        Config::set('arkscan.networks.development.knownWallets', 'http://some.url');

        $validator1 = Wallet::factory()->activeValidator()->create([
            'attributes' => [
                'username' => 'validator-name',
            ],
        ]);

        $validator2 = Wallet::factory()->activeValidator()->create([
            'address'    => '0x2a74550fC2e741118182B7ab020DC0B7Ed01e1db',
            'attributes' => [
                'username' => null,
            ],
        ]);

        $validator3 = Wallet::factory()->activeValidator()->create([
            'address'    => '0xC5a19e23E99bdFb7aae4301A009763AdC01c1b5B',
            'attributes' => [
                'username' => 'validator-3',
            ],
        ]);

        $voteTransaction1 = Transaction::factory()->vote($validator1->address)->create([
            'timestamp' => Carbon::parse('2023-09-18 03:41:04')->getTimestampMs(),
            'status'    => true,
        ]);

        $unvoteTransaction = Transaction::factory()->unvote()->create([
            'timestamp' => Carbon::parse('2023-09-18 04:41:05')->getTimestampMs(),
            'status'    => true,
        ]);

        $voteTransaction2 = Transaction::factory()->vote($validator2->address)->create([
            'timestamp' => Carbon::parse('2023-09-18 05:41:06')->getTimestampMs(),
            'status'    => true,
        ]);

        $unvoteTransaction2 = Transaction::factory()->unvote()->create([
            'timestamp' => Carbon::parse('2023-09-18 06:41:07')->getTimestampMs(),
            'status'    => true,
        ]);

        $voteTransaction3 = Transaction::factory()->vote($validator3->address)->create([
            'timestamp' => Carbon::parse('2023-09-18 07:41:06')->getTimestampMs(),
            'status'    => true,
        ]);

        Artisan::call('explorer:cache-known-wallets');

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($voteTransaction1, $voteTransaction2, $voteTransaction3, $unvoteTransaction, $unvoteTransaction2) {
                $reload->has('recentVotes.data', 5)
                    ->where('recentVotes.data.0.hash', $voteTransaction2->hash)
                    ->where('recentVotes.data.1.hash', $voteTransaction3->hash)
                    ->where('recentVotes.data.2.hash', $voteTransaction1->hash)
                    ->where('recentVotes.data.3.hash', $unvoteTransaction->hash)
                    ->where('recentVotes.data.4.hash', $unvoteTransaction2->hash);
            },
            queryString: [
                'sort'           => 'name',
                'sort-direction' => 'asc',
            ],
            reloadProps: 'recentVotes',
        );
    });

    it('should sort known name, then name, then address in descending order when missing names', function () {
        fakeKnownWallets();

        Config::set('arkscan.networks.development.knownWallets', 'http://some.url');

        $validator1 = Wallet::factory()->activeValidator()->create([
            'attributes' => [
                'username' => 'validator-name',
            ],
        ]);

        $validator2 = Wallet::factory()->activeValidator()->create([
            'address'    => '0x2a74550fC2e741118182B7ab020DC0B7Ed01e1db',
            'attributes' => [
                'username' => null,
            ],
        ]);

        $validator3 = Wallet::factory()->activeValidator()->create([
            'address'    => '0xC5a19e23E99bdFb7aae4301A009763AdC01c1b5B',
            'attributes' => [
                'username' => 'validator-3',
            ],
        ]);

        $voteTransaction1 = Transaction::factory()->vote($validator1->address)->create([
            'timestamp' => Carbon::parse('2023-09-18 03:41:04')->getTimestampMs(),
            'status'    => true,
        ]);

        $unvoteTransaction = Transaction::factory()->unvote()->create([
            'timestamp' => Carbon::parse('2023-09-18 04:41:05')->getTimestampMs(),
            'status'    => true,
        ]);

        $voteTransaction2 = Transaction::factory()->vote($validator2->address)->create([
            'timestamp' => Carbon::parse('2023-09-18 05:41:06')->getTimestampMs(),
            'status'    => true,
        ]);

        $unvoteTransaction2 = Transaction::factory()->unvote()->create([
            'timestamp' => Carbon::parse('2023-09-18 06:41:07')->getTimestampMs(),
            'status'    => true,
        ]);

        $voteTransaction3 = Transaction::factory()->vote($validator3->address)->create([
            'timestamp' => Carbon::parse('2023-09-18 07:41:06')->getTimestampMs(),
            'status'    => true,
        ]);

        Artisan::call('explorer:cache-known-wallets');

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($voteTransaction1, $voteTransaction2, $voteTransaction3, $unvoteTransaction, $unvoteTransaction2) {
                $reload->has('recentVotes.data', 5)
                    ->where('recentVotes.data.0.hash', $voteTransaction1->hash)
                    ->where('recentVotes.data.1.hash', $voteTransaction3->hash)
                    ->where('recentVotes.data.2.hash', $voteTransaction2->hash)
                    ->where('recentVotes.data.3.hash', $unvoteTransaction->hash)
                    ->where('recentVotes.data.4.hash', $unvoteTransaction2->hash);
            },
            queryString: [
                'sort'           => 'name',
                'sort-direction' => 'desc',
            ],
            reloadProps: 'recentVotes',
        );
    });
});

describe('Missed Blocks', function () {
    it('should have missed blocks', function () {
        $block1 = ForgingStats::factory()->create();
        $block2 = ForgingStats::factory()->create();

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($block1, $block2) {
                $reload->has('missedBlocks.data', 2)
                    ->where('missedBlocks.total', 2)
                    ->where('missedBlocks.current_page', 1)
                    ->where('missedBlocks.last_page', 1)
                    ->where('missedBlocks.meta', [
                        'pageName'  => 'page',
                        'urlParams' => [],
                    ])
                    ->where('missedBlocks.data', function ($blocks) use ($block1, $block2) {
                        $missedHeights = collect($blocks)->pluck('number');

                        return $missedHeights->contains($block1->missed_height) && $missedHeights->contains($block2->missed_height);
                    });
            },
        );
    });

    it('should pull missed blocks from sqlite databases', function () {
        Config::set('database.default', 'sqlite');
        Config::set('database.connections.sqlite.database', ':memory:');

        $this->artisan('migrate:fresh');

        $block1 = ForgingStats::factory()->create();
        $block2 = ForgingStats::factory()->create();

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($block1, $block2) {
                $reload->has('missedBlocks.data', 2)
                    ->where('missedBlocks.total', 2)
                    ->where('missedBlocks.current_page', 1)
                    ->where('missedBlocks.last_page', 1)
                    ->where('missedBlocks.meta', [
                        'pageName'  => 'page',
                        'urlParams' => [],
                    ])
                    ->where('missedBlocks.data', function ($blocks) use ($block1, $block2) {
                        $missedHeights = collect($blocks)->pluck('number');

                        return $missedHeights->contains($block1->missed_height) && $missedHeights->contains($block2->missed_height);
                    });
            },
        );
    });

    it('should sort height in ascending order', function () {
        $wallet1 = Wallet::factory()->activeValidator()->create([
            'attributes' => [
                'validatorVoteBalance' => (string) BigNumber::new(4000 * 1e18),
            ],
        ]);

        $wallet2 = Wallet::factory()->activeValidator()->create([
            'attributes' => [
                'validatorVoteBalance' => (string) BigNumber::new(4000 * 1e18),
            ],
        ]);

        ForgingStats::factory()->create([
            'address'       => $wallet1->address,
            'missed_height' => 100,
        ]);

        ForgingStats::factory()->create([
            'address'       => $wallet2->address,
            'missed_height' => 134,
        ]);

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($wallet1, $wallet2) {
                $reload->has('missedBlocks.data', 2)
                    ->where('missedBlocks.data.0.validator.address', $wallet1->address)
                    ->where('missedBlocks.data.1.validator.address', $wallet2->address);
            },
            queryString: [
                'sort'           => 'height',
                'sort-direction' => 'asc',
            ],
            reloadProps: 'missedBlocks',
        );
    });

    it('should sort height in descending order', function () {
        $wallet1 = Wallet::factory()->activeValidator()->create([
            'attributes' => [
                'validatorVoteBalance' => (string) BigNumber::new(4000 * 1e18),
            ],
        ]);

        $wallet2 = Wallet::factory()->activeValidator()->create([
            'attributes' => [
                'validatorVoteBalance' => (string) BigNumber::new(4000 * 1e18),
            ],
        ]);

        ForgingStats::factory()->create([
            'address'       => $wallet1->address,
            'missed_height' => 100,
        ]);

        ForgingStats::factory()->create([
            'address'       => $wallet2->address,
            'missed_height' => 134,
        ]);

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($wallet1, $wallet2) {
                $reload->has('missedBlocks.data', 2)
                    ->where('missedBlocks.data.0.validator.address', $wallet2->address)
                    ->where('missedBlocks.data.1.validator.address', $wallet1->address);
            },
            queryString: [
                'sort'           => 'height',
                'sort-direction' => 'desc',
            ],
            reloadProps: 'missedBlocks',
        );
    });

    it('should sort by age by default', function () {
        $wallet2 = Wallet::factory()->activeValidator()->create([
            'attributes' => [
                'validatorVoteBalance' => (string) BigNumber::new(4000 * 1e18),
            ],
        ]);

        $wallet1 = Wallet::factory()->activeValidator()->create([
            'attributes' => [
                'validatorVoteBalance' => (string) BigNumber::new(10000 * 1e18),
            ],
        ]);

        ForgingStats::factory()->create([
            'address'    => $wallet1->address,
            'timestamp'  => 100,
        ]);

        ForgingStats::factory()->create([
            'address'    => $wallet2->address,
            'timestamp'  => 134,
        ]);

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($wallet1, $wallet2) {
                $reload->has('missedBlocks.data', 2)
                    ->where('missedBlocks.data.0.validator.address', $wallet2->address)
                    ->where('missedBlocks.data.1.validator.address', $wallet1->address);
            },
            queryString: [
                'sort'           => 'age',
                'sort-direction' => 'desc',
            ],
            reloadProps: 'missedBlocks',
        );
    });

    it('should sort age in ascending order', function () {
        $wallet1 = Wallet::factory()->activeValidator()->create([
            'attributes' => [
                'validatorVoteBalance' => (string) BigNumber::new(10000 * 1e18),
            ],
        ]);

        $wallet2 = Wallet::factory()->activeValidator()->create([
            'attributes' => [
                'validatorVoteBalance' => (string) BigNumber::new(4000 * 1e18),
            ],
        ]);

        ForgingStats::factory()->create([
            'address'    => $wallet1->address,
            'timestamp'  => 100,
        ]);

        ForgingStats::factory()->create([
            'address'    => $wallet2->address,
            'timestamp'  => 134,
        ]);

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($wallet1, $wallet2) {
                $reload->has('missedBlocks.data', 2)
                    ->where('missedBlocks.data.0.validator.address', $wallet1->address)
                    ->where('missedBlocks.data.1.validator.address', $wallet2->address);
            },
            queryString: [
                'sort'           => 'age',
                'sort-direction' => 'asc',
            ],
            reloadProps: 'missedBlocks',
        );
    });

    it('should sort number of voters in ascending order', function () {
        $wallet1 = Wallet::factory()->activeValidator()->create([
            'attributes' => [
                'validatorVoteBalance' => (string) BigNumber::new(10000 * 1e18),
            ],
        ]);

        $wallet2 = Wallet::factory()->activeValidator()->create([
            'attributes' => [
                'validatorVoteBalance' => (string) BigNumber::new(4000 * 1e18),
            ],
        ]);

        $walletWithoutVoters = Wallet::factory()->activeValidator()->create([
            'attributes' => [
                'validatorVoteBalance' => (string) BigNumber::new(4000 * 1e18),
            ],
        ]);

        ForgingStats::factory()->create([
            'address'    => $wallet1->address,
        ]);

        ForgingStats::factory()->create([
            'address'    => $wallet2->address,
        ]);

        ForgingStats::factory()->create([
            'address'    => $walletWithoutVoters->address,
        ]);

        (new ValidatorCache())->setAllVoterCounts([
            $wallet1->address => 30,
            $wallet2->address => 10,
        ]);

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($wallet1, $wallet2, $walletWithoutVoters) {
                $reload->has('missedBlocks.data', 3)
                    ->where('missedBlocks.data.0.validator.address', $wallet2->address)
                    ->where('missedBlocks.data.1.validator.address', $wallet1->address)
                    ->where('missedBlocks.data.2.validator.address', $walletWithoutVoters->address);
            },
            queryString: [
                'sort'           => 'no_of_voters',
                'sort-direction' => 'asc',
            ],
            reloadProps: 'missedBlocks',
        );
    });

    it('should sort number of voters in descending order', function () {
        $wallet1 = Wallet::factory()->activeValidator()->create([
            'attributes' => [
                'validatorVoteBalance' => (string) BigNumber::new(10000 * 1e18),
            ],
        ]);

        $wallet2 = Wallet::factory()->activeValidator()->create([
            'attributes' => [
                'validatorVoteBalance' => (string) BigNumber::new(4000 * 1e18),
            ],
        ]);

        $walletWithoutVoters = Wallet::factory()->activeValidator()->create([
            'attributes' => [
                'validatorVoteBalance' => (string) BigNumber::new(4000 * 1e18),
            ],
        ]);

        ForgingStats::factory()->create([
            'address'    => $wallet1->address,
        ]);

        ForgingStats::factory()->create([
            'address'    => $wallet2->address,
        ]);

        ForgingStats::factory()->create([
            'address'    => $walletWithoutVoters->address,
        ]);

        (new ValidatorCache())->setAllVoterCounts([
            $wallet1->address => 30,
            $wallet2->address => 10,
        ]);

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($wallet1, $wallet2, $walletWithoutVoters) {
                $reload->has('missedBlocks.data', 3)
                    ->where('missedBlocks.data.0.validator.address', $wallet1->address)
                    ->where('missedBlocks.data.1.validator.address', $wallet2->address)
                    ->where('missedBlocks.data.2.validator.address', $walletWithoutVoters->address);
            },
            queryString: [
                'sort'           => 'no_of_voters',
                'sort-direction' => 'desc',
            ],
            reloadProps: 'missedBlocks',
        );
    });

    it('should handle no cached votes when sorting by number of voters', function () {
        $wallet1 = Wallet::factory()->activeValidator()->create([
            'attributes' => [
                'validatorVoteBalance' => (string) BigNumber::new(10000 * 1e18),
            ],
        ]);

        $wallet2 = Wallet::factory()->activeValidator()->create([
            'attributes' => [
                'validatorVoteBalance' => (string) BigNumber::new(4000 * 1e18),
            ],
        ]);

        $walletWithoutVoters = Wallet::factory()->activeValidator()->create([
            'attributes' => [
                'validatorVoteBalance' => (string) BigNumber::new(4000 * 1e18),
            ],
        ]);

        ForgingStats::factory()->create([
            'timestamp'  => 100,
            'address'    => $wallet1->address,
        ]);

        ForgingStats::factory()->create([
            'timestamp'  => 101,
            'address'    => $wallet2->address,
        ]);

        ForgingStats::factory()->create([
            'timestamp'  => 102,
            'address'    => $walletWithoutVoters->address,
        ]);

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($wallet1, $wallet2, $walletWithoutVoters) {
                $reload->has('missedBlocks.data', 3)
                    ->where('missedBlocks.data.0.validator.address', $wallet1->address)
                    ->where('missedBlocks.data.1.validator.address', $wallet2->address)
                    ->where('missedBlocks.data.2.validator.address', $walletWithoutVoters->address);
            },
            queryString: [
                'sort'           => 'no_of_voters',
                'sort-direction' => 'asc',
            ],
            reloadProps: 'missedBlocks',
        );
    });

    it('should sort votes & percentage in ascending order', function (string $sortKey) {
        $wallet1 = Wallet::factory()->activeValidator()->create([
            'attributes' => [
                'validatorVoteBalance' => (string) BigNumber::new(10000 * 1e18),
            ],
        ]);

        $wallet2 = Wallet::factory()->activeValidator()->create([
            'attributes' => [
                'validatorVoteBalance' => (string) BigNumber::new(4000 * 1e18),
            ],
        ]);

        ForgingStats::factory()->create([
            'address'    => $wallet1->address,
        ]);

        ForgingStats::factory()->create([
            'address'    => $wallet2->address,
        ]);

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($wallet1, $wallet2, $sortKey) {
                $reload->has('missedBlocks.data', 2)
                    ->where('missedBlocks.data.0.validator.address', $wallet2->address)
                    ->where('missedBlocks.data.1.validator.address', $wallet1->address);
            },
            queryString: [
                'sort'           => $sortKey,
                'sort-direction' => 'asc',
            ],
            reloadProps: 'missedBlocks',
        );
    })->with([
        'votes',
        'percentage_votes',
    ]);

    it('should sort votes & percentage in descending order', function (string $sortKey) {
        $wallet1 = Wallet::factory()->activeValidator()->create([
            'attributes' => [
                'validatorVoteBalance' => (string) BigNumber::new(10000 * 1e18),
            ],
        ]);

        $wallet2 = Wallet::factory()->activeValidator()->create([
            'attributes' => [
                'validatorVoteBalance' => (string) BigNumber::new(4000 * 1e18),
            ],
        ]);

        ForgingStats::factory()->create([
            'address'    => $wallet1->address,
        ]);

        ForgingStats::factory()->create([
            'address'    => $wallet2->address,
        ]);

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($wallet1, $wallet2, $sortKey) {
                $reload->has('missedBlocks.data', 2)
                    ->where('missedBlocks.data.0.validator.address', $wallet1->address)
                    ->where('missedBlocks.data.1.validator.address', $wallet2->address);
            },
            queryString: [
                'sort'           => $sortKey,
                'sort-direction' => 'desc',
            ],
            reloadProps: 'missedBlocks',
        );
    })->with([
        'votes',
        'percentage_votes',
    ]);

    it('should force descending if invalid query string value', function () {
        $wallet2 = Wallet::factory()->activeValidator()->create([
            'attributes' => [
                'username'             => 'validator-2',
                'validatorVoteBalance' => (string) BigNumber::new(4000 * 1e18),
            ],
        ]);

        $wallet1 = Wallet::factory()->activeValidator()->create([
            'attributes' => [
                'username'             => 'validator-1',
                'validatorVoteBalance' => (string) BigNumber::new(10000 * 1e18),
            ],
        ]);

        ForgingStats::factory()->create([
            'address'    => $wallet1->address,
            'timestamp'  => 100,
        ]);

        ForgingStats::factory()->create([
            'address'    => $wallet2->address,
            'timestamp'  => 134,
        ]);

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($wallet1, $wallet2) {
                $reload->has('missedBlocks.data', 2)
                    ->where('missedBlocks.data.0.validator.address', $wallet2->address)
                    ->where('missedBlocks.data.1.validator.address', $wallet1->address);
            },
            queryString: [
                'sort'           => 'name',
                'sort-direction' => 'testing',
            ],
            reloadProps: 'missedBlocks',
        );
    });

    it('should handle sorting several pages without cached data', function ($columnSortBy, $modelSortBy) {
        $validatorData = [];

        $sortByVotesData = [];

        foreach (range(1, 145) as $rank) {
            $wallet          = faker()->wallet;

            $sortByVotesData[$wallet['address']] = random_int(10, 100);

            $validatorData[] = [
                'balance'           => faker()->numberBetween(1, 1000) * 1e18,
                'nonce'             => faker()->numberBetween(1, 1000),
                'updated_at'        => faker()->numberBetween(1, 1000),
                'address'           => $wallet['address'],
                'public_key'        => $wallet['publicKey'],
                'attributes'        => json_encode([
                    'validatorRank'           => $rank,
                    'validatorVoteBalance'    => (string) BigNumber::new($sortByVotesData[$wallet['address']]),
                    'validatorProducedBlocks' => faker()->numberBetween(1, 1000),
                    'validatorMissedBlocks'   => faker()->numberBetween(1, 1000),
                ]),
            ];
        }

        Wallet::insert($validatorData);

        $missedBlocks       = [];
        $missedBlockCounter = 0;

        $missedBlocksData = [];

        $validators = Wallet::all();

        foreach ($validators as $validator) {
            $missedBlockCount = random_int(2, 4);
            foreach (range(1, $missedBlockCount) as $_) {
                $missedBlocksData[] = [
                    'timestamp'     => Timestamp::fromUnix(Carbon::now()->subHours($missedBlockCounter)->unix())->unix(),
                    'address'       => $validator->address,
                    'forged'        => faker()->boolean(),
                    'missed_height' => faker()->numberBetween(1, 10000),
                ];

                $missedBlockCounter++;
            }
        }

        ForgingStats::insert($missedBlocksData);

        $missedBlocks = ForgingStats::all();

        $missedBlocks = $missedBlocks->sort(function ($a, $b) use ($modelSortBy, $sortByVotesData) {
            if ($modelSortBy === 'validatorVoteBalance') {
                $aValue = $sortByVotesData[$a->address];
                $bValue = $sortByVotesData[$b->address];
            } else {
                $aValue = Arr::get($a, $modelSortBy);
                $bValue = Arr::get($b, $modelSortBy);
            }

            if (is_numeric($bValue) && is_numeric($aValue)) {
                if ((int) $aValue === (int) $bValue) {
                    return $b->timestamp - $a->timestamp;
                }

                return (int) $aValue - (int) $bValue;
            }

            $value = strcmp($aValue, $bValue);
            if ($value === 0) {
                return $b->timestamp - $a->timestamp;
            }

            return $value;
        });

        foreach (range(1, 4) as $page) {
            $pageData = $missedBlocks->chunk(25)->get($page - 1)->pluck('address');

            performValidatorsRequest(
                $this,
                reloadCallback: function (Assert $reload) use ($pageData) {
                    $reload->has('missedBlocks.data', 25)
                        ->where('missedBlocks.data', function ($blocks) use ($pageData) {
                            $addresses = collect($blocks)->pluck('validator.address');

                            return $addresses->values()->all() === $pageData->values()->all();
                        });
                },
                queryString: [
                    'sort'           => $columnSortBy,
                    'sort-direction' => 'asc',
                    'page'           => $page,
                ],
                reloadProps: 'missedBlocks',
            );
        }
    })->with([
        'height'           => ['height', 'missed_height'],
        'age'              => ['age', 'timestamp'],
        'no_of_voters'     => ['no_of_voters', 'timestamp'],
        'votes'            => ['votes', 'validatorVoteBalance'],
        'percentage_votes' => ['percentage_votes', 'validatorVoteBalance'],
    ]);

    it('should handle sorting several pages with cached data', function ($columnSortBy, $modelSortBy) {
        $this->freezeTime();
        $this->travelTo('2025-09-04 13:44:12');

        $validatorData = [];

        $sortByVotesData = [];

        foreach (range(1, 145) as $rank) {
            $wallet          = faker()->wallet;

            $sortByVotesData[$wallet['address']] = random_int(10, 100);

            $validatorData[] = [
                'balance'           => faker()->numberBetween(1, 1000) * 1e18,
                'nonce'             => faker()->numberBetween(1, 1000),
                'updated_at'        => faker()->numberBetween(1, 1000),
                'address'           => $wallet['address'],
                'public_key'        => $wallet['publicKey'],
                'attributes'        => json_encode([
                    'validatorRank'           => $rank,
                    'validatorVoteBalance'    => (string) BigNumber::new($sortByVotesData[$wallet['address']]),
                    'validatorProducedBlocks' => faker()->numberBetween(1, 1000),
                    'validatorMissedBlocks'   => faker()->numberBetween(1, 1000),
                ]),
            ];
        }

        Wallet::insert($validatorData);

        $voterCounts        = [];
        $missedBlocks       = [];
        $missedBlockCounter = 0;

        $missedBlocksData = [];

        $validators = Wallet::all();

        foreach ($validators as $validator) {
            $missedBlockCount = random_int(2, 4);
            foreach (range(1, $missedBlockCount) as $_) {
                $missedBlocksData[] = [
                    'timestamp'     => Timestamp::fromUnix(Carbon::now()->subHours($missedBlockCounter)->unix())->unix(),
                    'address'       => $validator->address,
                    'forged'        => faker()->boolean(),
                    'missed_height' => faker()->numberBetween(1, 10000),
                ];

                $sortByVotesData[$validator->address] = $validator->attributes['validatorVoteBalance'];

                $missedBlockCounter++;
            }

            $voterCounts[$validator->address] = random_int(10, 100);
        }

        ForgingStats::insert($missedBlocksData);

        $missedBlocks = ForgingStats::all();

        $validatorCache = new ValidatorCache();
        $validatorCache->setAllVoterCounts($voterCounts);

        $missedBlocks = $missedBlocks->sort(function ($a, $b) use ($modelSortBy, $voterCounts, $sortByVotesData) {
            if ($modelSortBy === 'no_of_voters') {
                $aValue = $voterCounts[$a->address];
                $bValue = $voterCounts[$b->address];
            } elseif ($modelSortBy === 'votes' || $modelSortBy === 'percentage_votes') {
                $aValue = $sortByVotesData[$a->address];
                $bValue = $sortByVotesData[$b->address];
            } else {
                $aValue = Arr::get($a, $modelSortBy);
                $bValue = Arr::get($b, $modelSortBy);
            }

            if (is_numeric($bValue) && is_numeric($aValue)) {
                if ((int) $aValue === (int) $bValue) {
                    return $b->timestamp - $a->timestamp;
                }

                return (int) $aValue - (int) $bValue;
            }

            $value = strcmp($aValue, $bValue);
            if ($value === 0) {
                return $b->timestamp - $a->timestamp;
            }

            return $value;
        });

        foreach (range(1, 4) as $page) {
            $pageData = $missedBlocks->chunk(25)->get($page - 1)->pluck('address');

            performValidatorsRequest(
                $this,
                reloadCallback: function (Assert $reload) use ($pageData) {
                    $reload->has('missedBlocks.data', 25)
                        ->where('missedBlocks.data', function ($blocks) use ($pageData) {
                            $addresses = collect($blocks)->pluck('validator.address');

                            return $addresses->values()->all() === $pageData->values()->all();
                        });
                },
                queryString: [
                    'sort'           => $columnSortBy,
                    'sort-direction' => 'asc',
                    'page'           => $page,
                ],
                reloadProps: 'missedBlocks',
            );
        }
    })->with([
        'height'           => ['height', 'missed_height'],
        'age'              => ['age', 'timestamp'],
        'no_of_voters'     => ['no_of_voters', 'no_of_voters'],
        'votes'            => ['votes', 'votes'],
        'percentage_votes' => ['percentage_votes', 'percentage_votes'],
    ]);

    it('should not sort for sqlite databases', function ($sortBy) {
        Config::set('database.default', 'sqlite');
        Config::set('database.connections.sqlite.database', ':memory:');

        $this->artisan('migrate:fresh');

        $wallet2 = Wallet::factory()->activeValidator()->create([
            'attributes' => [
                'username'             => 'validator-2',
                'validatorVoteBalance' => (string) BigNumber::new(4000 * 1e18),
            ],
        ]);

        $wallet1 = Wallet::factory()->activeValidator()->create([
            'attributes' => [
                'username'             => 'validator-1',
                'validatorVoteBalance' => (string) BigNumber::new(10000 * 1e18),
            ],
        ]);

        ForgingStats::factory()->create([
            'address'    => $wallet1->address,
            'timestamp'  => 100,
        ]);

        ForgingStats::factory()->create([
            'address'    => $wallet2->address,
            'timestamp'  => 134,
        ]);

        // Not missed
        ForgingStats::factory()->create([
            'address'       => $wallet2->address,
            'timestamp'     => 151,
            'missed_height' => null,
        ]);

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($wallet1, $wallet2) {
                $reload->has('missedBlocks.data', 2)
                    ->where('missedBlocks.data.0.validator.address', $wallet2->address)
                    ->where('missedBlocks.data.1.validator.address', $wallet1->address);
            },
            queryString: [
                'sort' => $sortBy,
            ],
            reloadProps: 'missedBlocks',
        );
    })->with([
        'height',
        'age',
        'no_of_voters',
        'votes',
        'percentage_votes',
    ]);
});

describe('Validators', function () {
    it('should provide validators data with pagination meta', function () {
        $activeValidator = Wallet::factory()->create([
            'address'    => 'address-active',
            'public_key' => 'public-key-active',
            'attributes' => [
                'username'                => 'active-validator',
                'validatorPublicKey'      => 'validator-public-active',
                'validatorRank'           => 2,
                'validatorResigned'       => false,
                'validatorVoteBalance'    => 10 * 1e8,
                'validatorProducedBlocks' => 100,
                'validatorMissedBlocks'   => 1,
            ],
        ]);

        $standbyRank = Network::validatorCount() + 2;

        $standbyValidator = Wallet::factory()->create([
            'address'    => 'address-standby',
            'public_key' => 'public-key-standby',
            'attributes' => [
                'username'                => 'standby-validator',
                'validatorPublicKey'      => 'validator-public-standby',
                'validatorRank'           => $standbyRank,
                'validatorResigned'       => false,
                'validatorVoteBalance'    => 20 * 1e8,
                'validatorProducedBlocks' => 200,
                'validatorMissedBlocks'   => 3,
            ],
        ]);

        Wallet::factory()->create([
            'address'    => 'address-non-validator',
            'public_key' => 'public-key-non-validator',
            'attributes' => [
                'username'           => 'non-validator',
                'validatorPublicKey' => null,
            ],
        ]);

        $walletCache = new WalletCache();
        $walletCache->setVoterCount($activeValidator->address, 5);
        $walletCache->setVoterCount($standbyValidator->address, 2);
        $walletCache->setMissedBlocks($activeValidator->address, 4);
        $walletCache->setMissedBlocks($standbyValidator->address, 1);
        $walletCache->setProductivity($activeValidator->address, 99.9);
        $walletCache->setProductivity($standbyValidator->address, 95);

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($activeValidator, $standbyValidator, $standbyRank) {
                $reload->has('validators.data', 2)
                    ->where('validators.total', 2)
                    ->where('validators.current_page', 1)
                    ->where('validators.last_page', 1)
                    ->where('validators.meta', [
                        'pageName'  => 'page',
                        'urlParams' => [],
                    ])
                    ->where('validators.perPageOptions', trans('tables.validators.validator_per_page_options'))
                    ->where('validators.noResultsMessage', null)
                    ->where('validators.data.0.address', $activeValidator->address)
                    ->where('validators.data.0.rank', 2)
                    ->where('validators.data.0.voterCount', 5)
                    ->where('validators.data.1.address', $standbyValidator->address)
                    ->where('validators.data.1.rank', $standbyRank)
                    ->where('validators.data.1.voterCount', 2);
            },
            queryString: [],
            reloadProps: 'validators',
        );
    });

    it('should return no filters message when all validator filters are disabled', function () {
        createValidatorWallet('validator-1', 1);

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) {
                $reload->where('validators.data', [])
                    ->where('validators.total', 0)
                    ->where('validators.noResultsMessage', trans('tables.validators.no_results.no_filters'));
            },
            queryString: [
                'active'   => 0,
                'standby'  => 0,
                'dormant'  => 0,
                'resigned' => 0,
            ],
            reloadProps: 'validators',
        );
    });

    it('should filter validators by standby flag', function () {
        $active = createValidatorWallet('validator-active', 1);

        $standbyRank = Network::validatorCount() + 1;
        $standby     = createValidatorWallet('validator-standby', $standbyRank);

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($standby, $active) {
                $reload->has('validators.data', 1)
                    ->where('validators.total', 1)
                    ->where('validators.data.0.address', $standby->address);
            },
            queryString: [
                'active'   => 0,
                'standby'  => 1,
                'dormant'  => 0,
                'resigned' => 0,
            ],
            reloadProps: 'validators',
        );
    });

    it('should filter validators by dormant flag', function () {
        createValidatorWallet('validator-active', 1);

        $dormant = createValidatorWalletWithAttributes('validator-dormant', Network::validatorCount() + 5, [
            'validatorPublicKey' => '',
        ]);

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($dormant) {
                $reload->has('validators.data', 1)
                    ->where('validators.total', 1)
                    ->where('validators.data.0.address', $dormant->address);
            },
            queryString: [
                'active'   => 0,
                'standby'  => 0,
                'dormant'  => 1,
                'resigned' => 0,
            ],
            reloadProps: 'validators',
        );
    });

    it('should filter validators by resigned flag', function () {
        createValidatorWallet('validator-active', 1);

        $resigned = createValidatorWalletWithAttributes('validator-resigned', 10, [
            'validatorResigned' => true,
        ]);

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($resigned) {
                $reload->has('validators.data', 1)
                    ->where('validators.total', 1)
                    ->where('validators.data.0.address', $resigned->address);
            },
            queryString: [
                'active'   => 0,
                'standby'  => 0,
                'dormant'  => 0,
                'resigned' => 1,
            ],
            reloadProps: 'validators',
        );
    });

    it('should support sorting validators by rank in descending order', function () {
        $first  = createValidatorWallet('validator-1', 1);
        $second = createValidatorWallet('validator-2', 2);

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($second, $first) {
                $reload->has('validators.data', 2)
                    ->where('validators.data.0.address', $second->address)
                    ->where('validators.data.1.address', $first->address);
            },
            queryString: [
                'sort'           => 'rank',
                'sort-direction' => 'desc',
            ],
            reloadProps: 'validators',
        );
    });

    it('should support sorting validators by name', function () {
        $a = createValidatorWalletWithAttributes('validator-b', 1, ['username' => 'b']);
        $b = createValidatorWalletWithAttributes('validator-a', 2, ['username' => 'a']);

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($b, $a) {
                $reload->has('validators.data', 2)
                    ->where('validators.data.0.address', $b->address)
                    ->where('validators.data.1.address', $a->address);
            },
            queryString: [
                'sort' => 'name',
            ],
            reloadProps: 'validators',
        );
    });

    it('should support sorting validators by votes and percentage votes', function () {
        $low  = createValidatorWalletWithAttributes('validator-low', 1, ['validatorVoteBalance' => 5 * 1e8]);
        $high = createValidatorWalletWithAttributes('validator-high', 2, ['validatorVoteBalance' => 50 * 1e8]);

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($high, $low) {
                $reload->has('validators.data', 2)
                    ->where('validators.data.0.address', $high->address)
                    ->where('validators.data.1.address', $low->address);
            },
            queryString: [
                'sort'           => 'votes',
                'sort-direction' => 'desc',
            ],
            reloadProps: 'validators',
        );

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($high, $low) {
                $reload->has('validators.data', 2)
                    ->where('validators.data.0.address', $high->address)
                    ->where('validators.data.1.address', $low->address);
            },
            queryString: [
                'sort'           => 'percentage_votes',
                'sort-direction' => 'desc',
            ],
            reloadProps: 'validators',
        );
    });

    it('should support sorting validators by number of voters', function () {
        $low  = createValidatorWallet('validator-low', 1);
        $high = createValidatorWallet('validator-high', 2);

        $validatorCache = new ValidatorCache();
        $validatorCache->setAllVoterCounts([
            $low->address  => 1,
            $high->address => 10,
        ]);

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($high, $low) {
                $reload->has('validators.data', 2)
                    ->where('validators.data.0.address', $high->address)
                    ->where('validators.data.1.address', $low->address);
            },
            queryString: [
                'sort'           => 'no_of_voters',
                'sort-direction' => 'desc',
            ],
            reloadProps: 'validators',
        );
    });

    it('should support sorting validators by missed blocks', function () {
        $low  = createValidatorWallet('validator-low', 1);
        $high = createValidatorWallet('validator-high', 2);

        ForgingStats::factory()->create([
            'address' => $high->address,
        ]);

        ForgingStats::factory()->create([
            'address' => $high->address,
        ]);

        ForgingStats::factory()->create([
            'address' => $low->address,
        ]);

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($high, $low) {
                $reload->has('validators.data', 2)
                    ->where('validators.data.0.address', $high->address)
                    ->where('validators.data.1.address', $low->address);
            },
            queryString: [
                'sort'           => 'missed_blocks',
                'sort-direction' => 'desc',
            ],
            reloadProps: 'validators',
        );
    });

    it('should return no results message when there are no validators', function () {
        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) {
                $reload->where('validators.data', [])
                    ->where('validators.total', 0)
                    ->where('validators.noResultsMessage', trans('tables.validators.no_results.no_results'));
            },
            queryString: [],
            reloadProps: 'validators',
        );
    });

    it('should respect validators page query parameter', function () {
        createValidatorWallet('validator-1', 1);
        $second = createValidatorWallet('validator-2', 2);

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($second) {
                $reload->where('validators.current_page', 2)
                    ->where('validators.per_page', 1)
                    ->where('validators.data.0.address', $second->address);
            },
            queryString: [
                'page'            => 2,
                'per-page'        => 1,
            ],
            reloadProps: 'validators',
        );
    });

    it('should fall back to the default page parameter when validators page is missing', function () {
        createValidatorWallet('validator-1', 1);
        $second = createValidatorWallet('validator-2', 2);

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) use ($second) {
                $reload->where('validators.current_page', 2)
                    ->where('validators.per_page', 1)
                    ->where('validators.data.0.address', $second->address);
            },
            queryString: [
                'page'     => 2,
                'per-page' => 1,
            ],
            reloadProps: 'validators',
        );
    });

    it('should honor the per-page parameter for validators', function () {
        createValidatorWallet('validator-1', 1);
        createValidatorWallet('validator-2', 2);
        createValidatorWallet('validator-3', 3);

        performValidatorsRequest(
            $this,
            reloadCallback: function (Assert $reload) {
                $reload->where('validators.per_page', 2)
                    ->has('validators.data', 2);
            },
            queryString: [
                'per-page' => 2,
            ],
            reloadProps: 'validators',
        );
    });
});
