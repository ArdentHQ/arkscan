<?php

declare(strict_types=1);

use App\Models\ForgingStats;
use Illuminate\Support\Facades\Cache;
use Laravel\Dusk\Browser;

beforeEach(function () {
    $this->withoutExceptionHandling();
});

afterEach(function () {
    Cache::tags(['dusk'])->flush();
});

describe('Missed Blocks Tab', function () {
    it('should handle no missed blocks', function ($resolution) {
        $this->browse(function (Browser $browser) use ($resolution) {
            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('validators')
                ->click('button#tab-missed-blocks')
                ->waitForText(trans('tables.missed-blocks.no_results'));
        });
    })->with('resolutions');

    it('should display missed blocks', function ($resolution) {
        ForgingStats::factory()->create([
            'missed_height' => 100,
        ]);

        ForgingStats::factory()->create([
            'missed_height' => 134,
        ]);

        $this->browse(function (Browser $browser) use ($resolution) {
            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('validators')
                ->click('button#tab-missed-blocks')
                ->waitForText('2 results', ignoreCase: true);
        });
    })->with('resolutions');

    it('should go to page 2', function ($resolution) {
        for ($i = 14234; $i < 14234 + 30; $i++) {
            ForgingStats::factory()
                ->create([
                    'missed_height' => $i,
                ]);
        }

        $this->browse(function (Browser $browser) use ($resolution) {
            $sortedBlocks = ForgingStats::orderBy('missed_height', 'desc')
                ->whereNotNull('missed_height');

            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('validators')
                ->click('button#tab-missed-blocks')
                ->waitForText('30 results', ignoreCase: true)
                ->click('[data-testid="pagination:next-page"] button')
                ->waitForText('Page 2 of 2')
                ->assertQueryStringHas('page', '2');

            foreach ($sortedBlocks->skip(25)->take(5)->get() as $block) {
                $browser->assertSee(number_format($block->missed_height));
            }
        });
    })->with('resolutions');

    it('should reset to page 1 on per-page change', function ($resolution) {
        for ($i = 14234; $i < 14234 + 30; $i++) {
            ForgingStats::factory()
                ->create([
                    'missed_height' => $i,
                ]);
        }

        $this->browse(function (Browser $browser) use ($resolution) {
            $sortedBlocks = ForgingStats::orderBy('missed_height', 'desc')
                ->whereNotNull('missed_height');

            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('validators', ['tab' => 'missed-blocks', 'page' => 2])
                ->waitForText('30 results', ignoreCase: true)
                ->assertSee('Page 2 of 2')
                ->click('[data-testid="pagination:per-page-dropdown:button"]')
                ->waitForTextIn('[data-testid="pagination:per-page-dropdown:dropdown"]', '10')
                ->clickAtXPath('//div[@data-testid="pagination:per-page-dropdown:dropdown"]//span[.//text()="10"]')
                ->waitForText('Page 1 of 3');

            foreach ($sortedBlocks->take(10)->get() as $block) {
                $browser->assertSee(number_format($block->missed_height));
            }
        });
    })->with('resolutions');
});

dataset('resolutions', [
    'desktop' => [['width' => 1280, 'height' => 1024]],
    'lg'      => [['width' => 1024, 'height' => 768]],
    'md-lg'   => [['width' => 960, 'height' => 667]],
    'md'      => [['width' => 768, 'height' => 1024]],
    'sm'      => [['width' => 640, 'height' => 960]],
    'xs'      => [['width' => 370, 'height' => 844]],
]);
