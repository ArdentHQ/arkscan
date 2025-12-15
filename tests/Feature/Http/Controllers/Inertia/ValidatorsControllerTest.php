<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Http\Controllers\Inertia\ValidatorsController;
use App\Models\ForgingStats;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Cache\CryptoDataCache;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\NetworkStatusBlockCache;
use App\Services\Cache\ValidatorCache;
use App\Services\Cache\WalletCache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Inertia\Testing\AssertableInertia as Assert;

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

it('should render the page without any errors', function () {
    performValidatorsRequest($this);
});

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
