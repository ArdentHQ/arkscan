<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\ForgingStats;
use App\Models\Transaction;
use Carbon\Carbon;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->withoutExceptionHandling();
});

function performBlocksListRequest($context, $withReload = true, $pageCallback = null, $reloadCallback = null, array $queryString = [], string $reloadProps = 'missedBlocks'): mixed
{
    return $context->get(route('blocks', $queryString))
        ->assertOk()
        ->assertInertia(function (Assert $page) use ($pageCallback, $withReload, $reloadCallback, $reloadProps) {
            $page->missing('missedBlocks')
                ->component('Blocks/List');

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

it('should render the page without any errors', function () {
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
        withReload: false,
    );
});
