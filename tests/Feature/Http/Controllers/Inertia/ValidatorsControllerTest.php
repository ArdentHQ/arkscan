<?php

declare(strict_types=1);

use App\Models\ForgingStats;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->withoutExceptionHandling();
});

function performWalletRequest($context, $withReload = true, $pageCallback = null, $reloadCallback = null, array $queryString = []): mixed
{
    return $context->get(route('validators', $queryString))
        ->assertOk()
        ->assertInertia(function (Assert $page) use ($pageCallback, $withReload, $reloadCallback) {
            $page->missing('missedBlocks')
                ->component('Validators/Validators');

            if (is_callable($pageCallback)) {
                $pageCallback($page);
            }

            if (! $withReload) {
                return;
            }

            $page->reloadOnly('missedBlocks', function (Assert $reload) use ($reloadCallback) {
                if (is_callable($reloadCallback)) {
                    $reloadCallback($reload);
                }
            });
        });
}

it('should render the page without any errors', function () {
    performWalletRequest($this);
});

it('should have missed blocks', function () {
    $block1 = ForgingStats::factory()->create();
    $block2 = ForgingStats::factory()->create();

    performWalletRequest(
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
