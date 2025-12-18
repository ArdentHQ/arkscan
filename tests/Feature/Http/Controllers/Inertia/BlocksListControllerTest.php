<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\ForgingStats;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Cache\WalletCache;
use App\ViewModels\ViewModelFactory;
use Carbon\Carbon;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->withoutExceptionHandling();
});

function performBlocksListRequest($context, $pageCallback = null, array $queryString = []): mixed
{
    return $context->get(route('blocks', $queryString))
        ->assertOk()
        ->assertInertia(function (Assert $page) use ($pageCallback) {
            $page->missing('missedBlocks')
                ->component('Blocks/List');

            if (is_callable($pageCallback)) {
                $pageCallback($page);
            }
        });
}

it('should render the page without any errors', function () {
    Block::factory()->create([
        'timestamp' => Carbon::parse('2021-04-14 13:02:04')->getTimestampMs(),
        'reward'    => 2 * 1e18,
    ]);

    performBlocksListRequest($this);
});

it('should statistics data', function () {
    $this->travelTo('2021-04-14 16:02:04');

    Block::factory()->create([
        'timestamp'          => Carbon::parse('2021-04-14 13:02:04')->getTimestampMs(),
        'reward'             => 2 * 1e18,
        'transactions_count' => 904,
    ]);

    foreach (range(1, 19) as $seconds) {
        ForgingStats::factory()->create([
            'timestamp'     => Carbon::parse('2021-04-14 13:02:04')->subSecond($seconds)->getTimestampMs(),
            'missed_height' => 1,
        ]);
    }

    $blocks = Block::factory(147)->create([
        'timestamp' => Carbon::parse('2021-04-14 13:02:04')->getTimestampMs(),
        'reward'    => 2 * 1e18,
    ]);

    foreach ($blocks as $block) {
        Transaction::factory()->create([
            'block_hash' => $block->hash,
            'value'      => 13 * 1e18,
        ]);
    }

    $block = Block::factory()->create([
        'timestamp' => Carbon::parse('2021-04-14 13:02:04')->getTimestampMs(),
        'reward'    => 2 * 1e18,
    ]);

    Transaction::factory()->create([
        'block_hash' => $block->hash,
        'value'      => 904 * 1e18,
    ]);

    $blocks = Block::factory(12)->create([
        'timestamp' => Carbon::parse('2021-04-13 13:02:04')->getTimestampMs(),
        'reward'    => 2 * 1e18,
    ]);

    foreach ($blocks as $block) {
        Transaction::factory()->create([
            'block_hash' => $block->hash,
            'value'      => 123 * 1e18,
        ]);
    }

    $blockCount = Block::where('timestamp', '>', Carbon::parse('2021-04-13 16:02:04')->getTimestampMs())->count();

    performBlocksListRequest(
        $this,
        pageCallback: function (Assert $page) use ($blockCount) {
            $page->where('statistics', [
                'forgedCount'     => $blockCount,
                'missedCount'     => 19,
                'totalRewards'    => 2 * $blockCount,
                'maxTransactions' => 904,
            ]);
        },
    );
});

it('should cache the transaction stats for 5 minutes', function () {
    $this->travelTo('2021-04-14 16:02:04');

    $blocks = Block::factory(148)->create([
        'timestamp'          => Carbon::parse('2021-04-14 13:02:04')->getTimestampMs(),
        'reward'             => 2 * 1e18,
        'transactions_count' => 13,
    ]);

    foreach ($blocks as $block) {
        Transaction::factory()
            ->create([
                'block_hash'   => $block->hash,
                'block_number' => $block->number,
                'value'        => 13 * 1e18,
            ]);
    }

    foreach (range(1, 19) as $seconds) {
        ForgingStats::factory()->create([
            'timestamp'     => Carbon::parse('2021-04-14 13:02:04')->subSecond($seconds)->getTimestampMs(),
            'missed_height' => 1,
        ]);
    }

    performBlocksListRequest(
        $this,
        pageCallback: function (Assert $page) {
            $page->where('statistics', [
                'forgedCount'     => 148,
                'missedCount'     => 19,
                'totalRewards'    => 2 * 148,
                'maxTransactions' => 13,
            ]);
        },
    );

    $blocks = Block::factory(12)->create([
        'timestamp'          => Carbon::parse('2021-04-14 13:03:04')->getTimestampMs(),
        'reward'             => 2 * 1e18,
        'transactions_count' => 24,
    ]);

    foreach ($blocks as $block) {
        Transaction::factory()
            ->create([
                'block_hash'   => $block->hash,
                'block_number' => $block->number,
                'value'        => 14 * 1e18,
            ]);
    }

    foreach (range(1, 2) as $seconds) {
        ForgingStats::factory()->create([
            'timestamp'     => Carbon::parse('2021-04-14 13:03:04')->subSecond($seconds)->getTimestampMs(),
            'missed_height' => 1,
        ]);
    }

    performBlocksListRequest(
        $this,
        pageCallback: function (Assert $page) {
            $page->where('statistics', [
                'forgedCount'     => 148,
                'missedCount'     => 19,
                'totalRewards'    => 2 * 148,
                'maxTransactions' => 13,
            ]);
        },
    );

    $this->travelTo('2021-04-14 16:09:04');

    performBlocksListRequest(
        $this,
        pageCallback: function (Assert $page) {
            $page->where('statistics', [
                'forgedCount'     => 160,
                'missedCount'     => 21,
                'totalRewards'    => 2 * 160,
                'maxTransactions' => 24,
            ]);
        },
    );
});

it('should list the first page of records', function () {
    $this->travelTo(Carbon::parse('2023-07-12 00:00:00'));

    $cache = new WalletCache();

    foreach (range(0, 40) as $index) {
        $this->travel(8)->seconds();

        $block = Block::factory()->create([
            'timestamp' => Carbon::now()->timestamp,
            'number'    => $index + 1,
        ]);

        $cache->setWalletNameByAddress($block->proposer, 'test-username-'.($index + 1));
    }

    performBlocksListRequest(
        $this,
        pageCallback: function (Assert $page) {
            foreach (ViewModelFactory::paginate(Block::withScope(OrderByTimestampScope::class)->paginate())->items() as $index => $block) {
                $page->where("blocks.data.{$index}.hash", $block->hash());
            }
        },
    );
});

it('should list the last page of records', function () {
    $this->travelTo(Carbon::parse('2023-07-12 00:00:00'));

    $cache = new WalletCache();

    foreach (range(0, 40) as $index) {
        $this->travel(8)->seconds();

        $block = Block::factory()->create([
            'timestamp' => Carbon::now()->timestamp,
            'number'    => $index + 1,
        ]);

        $cache->setWalletNameByAddress($block->proposer, 'test-username-'.($index + 1));
    }

    performBlocksListRequest(
        $this,
        pageCallback: function (Assert $page) {
            $blocks = Block::withScope(OrderByTimestampScope::class)
                ->paginate(25, ['*'], 'page', 2, Block::count());

            foreach (ViewModelFactory::paginate($blocks)->items() as $index => $block) {
                $page->where("blocks.data.{$index}.hash", $block->hash());
            }
        },
        queryString: ['page' => 2],
    );
});

it('should handle a lot of blocks', function () {
    $this->travelTo(Carbon::parse('2023-07-12 00:00:00'));

    $wallet = Wallet::factory()->create();

    foreach (range(1, 4000) as $index) {
        $this->travel(8)->seconds();

        Block::factory()->create([
            'proposer'          => $wallet->address,
            'timestamp'         => Carbon::now()->timestamp,
            'number'            => $index,
        ]);
    }

    expect(Block::count())->toBe(4000);

    performBlocksListRequest(
        $this,
        pageCallback: function (Assert $page) {
            $blocks = Block::withScope(OrderByTimestampScope::class)
                ->paginate(25, ['*'], 'page', 159, Block::count());

            foreach (ViewModelFactory::paginate($blocks)->items() as $index => $block) {
                $page->where("blocks.data.{$index}.hash", $block->hash());
            }
        },
        queryString: ['page' => 159],
    );
});

it('should handle snapshot of blocks', function () {
    $this->travelTo(Carbon::parse('2023-07-12 00:00:00'));

    $cache = new WalletCache();

    foreach (range(1001, 1511) as $index) {
        $this->travel(8)->seconds();

        $block = Block::factory()->create([
            'timestamp' => Carbon::now()->timestamp,
            'number'    => $index + 1,
        ]);

        $cache->setWalletNameByAddress($block->proposer, 'test-username-'.($index + 1));
    }

    $blockCount = Block::count();
    $pageCount  = intval(ceil($blockCount / 25));

    performBlocksListRequest(
        $this,
        pageCallback: function (Assert $page) use ($pageCount) {
            $page->where('blocks.total', 511)
                ->where('blocks.per_page', 25)
                ->where('blocks.current_page', 1)
                ->where('blocks.last_page', $pageCount);
        },
        queryString: ['per-page' => 25],
    );
});

it('should list the last page of a snapshot', function () {
    $this->travelTo(Carbon::parse('2023-07-12 00:00:00'));

    $cache = new WalletCache();

    foreach (range(1001, 1511) as $index) {
        $this->travel(8)->seconds();

        $block = Block::factory()->create([
            'timestamp' => Carbon::now()->timestamp,
            'number'    => $index + 1,
        ]);

        $cache->setWalletNameByAddress($block->proposer, 'test-username-'.($index + 1));
    }

    $blockCount = Block::count();
    $pageCount  = ceil($blockCount / 25);

    performBlocksListRequest(
        $this,
        pageCallback: function (Assert $page) use ($pageCount, $blockCount) {
            $blocks = Block::withScope(OrderByTimestampScope::class)
                ->paginate(25, ['*'], 'page', $pageCount, $blockCount);

            $page->has('blocks.data', 11);

            foreach (ViewModelFactory::paginate($blocks)->items() as $index => $block) {
                $page->where("blocks.data.{$index}.hash", $block->hash());
            }
        },
        queryString: ['page' => $pageCount, 'per-page' => 25],
    );
});

it('should show no results message if no blocks', function () {
    performBlocksListRequest(
        $this,
        pageCallback: function (Assert $page) {
            $page->has('blocks.data', 0)
                ->where('blocks.total', 0)
                ->where('blocks.noResultsMessage', trans('tables.blocks.no_results'));
        },
    );
});
