<?php

declare(strict_types=1);

use App\Contracts\RoundRepository as ContractsRoundRepository;
use App\Repositories\RoundRepository;
use App\Services\Cache\WalletCache;
use App\ViewModels\WalletViewModel;
use Carbon\Carbon;
use Laravel\Dusk\Browser;
use function Tests\createPartialRound;
use function Tests\createRealisticRound;
use function Tests\createRoundEntry;
use function Tests\getRoundValidators;

beforeEach(function () {
    $this->withoutExceptionHandling();
});

describe('Monitor', function () {
    beforeEach(function () {
        $this->activeValidators = require dirname(dirname(dirname(__DIR__))).'/fixtures/forgers.php';
    });

    it('should show warning icon for validators missing blocks - minutes', function ($resolution) {
        $this->freezeTime();

        [0 => $validators] = createRealisticRound([
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 48, true),
            ],
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 48, true),
            ],
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 48, true),
            ],
        ], $this);

        foreach ($validators as $validator) {
            $block = $validator->blocks()->orderBy('number', 'desc')->first();

            (new WalletCache())->setLastBlock($validator->address, [
                'hash'                   => $block['hash'],
                'number'                 => $block['number']->toNumber(),
                'timestamp'              => $block['timestamp'],
                'proposer'               => $validator->address,
            ]);
        }

        $validator = (new WalletViewModel($validators->get(4)));

        expect($validator->performance())->toBe([false, false]);

        $this->browse(function (Browser $browser) use ($validator, $resolution) {
            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('validator-monitor');

            $missedWarningSelector = 'div[data-testid="validator-monitor:missed-warning-'.$validator->address().'"]';
            if ($resolution['width'] <= 640) {
                $missedWarningSelector = 'div[data-testid="validator-monitor:missed-warning-'.$validator->address().':mobile"]';
            }

            $browser->waitFor($missedWarningSelector)
                ->mouseOver($missedWarningSelector)
                ->waitForText('Validator last forged 207 blocks ago (~ 28 min)');
        });
    })->with('resolutions');

    it('should show warning icon for validators missing blocks - hours', function ($resolution) {
        $this->travelTo(Carbon::now()->subHours(1));

        $this->freezeTime();

        [0 => $validators] = createRealisticRound([
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 48, true),
            ],
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 48, true),
            ],
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 48, true),
            ],
        ], $this);

        foreach ($validators as $validator) {
            $block = $validator->blocks()->orderBy('number', 'desc')->first();

            (new WalletCache())->setLastBlock($validator->address, [
                'hash'                   => $block['hash'],
                'number'                 => $block['number']->toNumber(),
                'timestamp'              => $block['timestamp'],
                'proposer'               => $validator->address,
            ]);
        }

        $validator = (new WalletViewModel($validators->get(4)));

        expect($validator->performance())->toBe([false, false]);

        $this->browse(function (Browser $browser) use ($validator, $resolution) {
            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('validator-monitor');

            $missedWarningSelector = 'div[data-testid="validator-monitor:missed-warning-'.$validator->address().'"]';
            if ($resolution['width'] <= 640) {
                $missedWarningSelector = 'div[data-testid="validator-monitor:missed-warning-'.$validator->address().':mobile"]';
            }

            $browser->waitFor($missedWarningSelector)
                ->mouseOver($missedWarningSelector)
                ->waitForText('Validator last forged 207 blocks ago (~ 1h 28 min)', 20);
        });
    })->with('resolutions');

    it('should show warning icon for validators missing blocks - days', function ($resolution) {
        $this->travelTo(Carbon::now()->subDays(2));

        $this->freezeTime();

        [0 => $validators] = createRealisticRound([
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 48, true),
            ],
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 48, true),
            ],
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 48, true),
            ],
        ], $this);

        foreach ($validators as $validator) {
            $block = $validator->blocks()->orderBy('number', 'desc')->first();

            (new WalletCache())->setLastBlock($validator->address, [
                'hash'                   => $block['hash'],
                'number'                 => $block['number']->toNumber(),
                'timestamp'              => $block['timestamp'],
                'proposer'               => $validator->address,
            ]);
        }

        $validator = (new WalletViewModel($validators->get(4)));

        expect($validator->performance())->toBe([false, false]);

        $this->browse(function (Browser $browser) use ($validator, $resolution) {
            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('validator-monitor');

            $missedWarningSelector = 'div[data-testid="validator-monitor:missed-warning-'.$validator->address().'"]';
            if ($resolution['width'] <= 640) {
                $missedWarningSelector = 'div[data-testid="validator-monitor:missed-warning-'.$validator->address().':mobile"]';
            }

            $browser->waitFor($missedWarningSelector)
                ->mouseOver($missedWarningSelector)
                ->waitForText('Validator last forged 207 blocks ago (more than a day)', 20);
        });
    })->with('resolutions');
});

describe('Data Boxes', function () {
    beforeEach(function () {
        $this->app->bind(ContractsRoundRepository::class, function (): RoundRepository {
            return new RoundRepository();
        });

        $this->travelTo(Carbon::parse('2022-08-22 00:00'));
        $this->freezeTime();
    });

    it('should calculate forged correctly with current round', function ($resolution) {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [$validators, $round, $height] = createRealisticRound([
            array_fill(0, 53, true),
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 48, true),
            ],
        ], $this);

        for ($i = 0; $i < 3; $i++) {
            createRoundEntry($round, $height, $validators);
            $validatorsOrder = getRoundValidators(false, $round);
            $validatorIndex  = $validatorsOrder->search(fn ($validator) => $validator['address'] === $validators->get(4)->address);
            if ($validatorIndex < 51) {
                break;
            }

            [$validators, $round, $height] = createRealisticRound([
                array_fill(0, 53, true),
                [
                    ...array_fill(0, 4, true),
                    false,
                    ...array_fill(0, 48, true),
                ],
            ], $this);
        }

        createPartialRound($round, $height, 51, $this, [], [$validators->get(4)->address]);

        expect((new WalletViewModel($validators->get(4)))->performance())->toBe([false, true]);

        foreach ($validators as $validator) {
            $block = $validator->blocks()->orderBy('number', 'desc')->first();

            (new WalletCache())->setLastBlock($validator->address, [
                'hash'                   => $block['hash'],
                'number'                 => $block['number']->toNumber(),
                'timestamp'              => $block['timestamp'],
                'proposer'               => $validator->address,
            ]);
        }

        $this->browse(function (Browser $browser) use ($resolution) {
            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('validator-monitor')
                ->waitForText(' / 53 Blocks', 10)
                ->assertEquals('[data-testid="validator-monitor:forging-count"] span', '53')
                ->assertEquals('[data-testid="validator-monitor:missed-count"] span', '0')
                ->assertEquals('[data-testid="validator-monitor:not-forging-count"] span', '0');
        });
    })->with('resolutions');

    it('should calculate forged correctly for previous rounds', function ($resolution) {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [0 => $validators] = createRealisticRound([
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 48, true),
            ],
            array_fill(0, 53, true),
            array_fill(0, 53, true),
        ], $this);

        expect((new WalletViewModel($validators->get(4)))->performance())->toBe([true, true]);

        $this->browse(function (Browser $browser) use ($resolution) {
            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('validator-monitor')
                ->waitForText(' / 53 Blocks', 10)
                ->assertEquals('[data-testid="validator-monitor:forging-count"] span', '53')
                ->assertEquals('[data-testid="validator-monitor:missed-count"] span', '0')
                ->assertEquals('[data-testid="validator-monitor:not-forging-count"] span', '0');
        });
    })->with('resolutions');

    it('should calculate missed correctly with current round', function ($resolution) {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [$validators, $round, $height] = createRealisticRound([
            array_fill(0, 53, true),
            array_fill(0, 53, true),
            array_fill(0, 53, true),
        ], $this);

        for ($i = 0; $i < 3; $i++) {
            createRoundEntry($round, $height, $validators);
            $validatorsOrder = getRoundValidators(false, $round);
            $validatorIndex  = $validatorsOrder->search(fn ($validator) => $validator['address'] === $validators->get(4)->address);
            if ($validatorIndex < 51) {
                break;
            }

            [$validators, $round, $height] = createRealisticRound([
                array_fill(0, 53, true),
            ], $this);
        }

        createPartialRound($round, $height, 51, $this, [$validators->get(4)->address], [$validators->get(4)->address]);

        expect((new WalletViewModel($validators->get(4)))->performance())->toBe([true, false]);

        $this->browse(function (Browser $browser) use ($resolution) {
            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('validator-monitor')
                ->waitForText(' / 53 Blocks', 10)
                ->assertEquals('[data-testid="validator-monitor:forging-count"] span', '52')
                ->assertEquals('[data-testid="validator-monitor:missed-count"] span', '1')
                ->assertEquals('[data-testid="validator-monitor:not-forging-count"] span', '0');
        });
    })->with('resolutions');

    it('should calculate missed correctly for previous rounds', function ($resolution) {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [0 => $validators] = createRealisticRound([
            array_fill(0, 53, true),
            array_fill(0, 53, true),
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 48, true),
            ],
        ], $this);

        expect((new WalletViewModel($validators->get(4)))->performance())->toBe([true, false]);

        $this->browse(function (Browser $browser) use ($resolution) {
            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('validator-monitor')
                ->waitForText(' / 53 Blocks', 10)
                ->assertEquals('[data-testid="validator-monitor:forging-count"] span', '52')
                ->assertEquals('[data-testid="validator-monitor:missed-count"] span', '1')
                ->assertEquals('[data-testid="validator-monitor:not-forging-count"] span', '0');
        });
    })->with('resolutions');

    it('should calculate not forging correctly with current round', function ($resolution) {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [$validators, $round, $height] = createRealisticRound([
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 48, true),
            ],
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 48, true),
            ],
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 48, true),
            ],
        ], $this);

        for ($i = 0; $i < 3; $i++) {
            createRoundEntry($round, $height, $validators);
            $validatorsOrder = getRoundValidators(false, $round);
            $validatorIndex  = $validatorsOrder->search(fn ($validator) => $validator['address'] === $validators->get(4)->address);
            if ($validatorIndex < 51) {
                break;
            }

            [$validators, $round, $height] = createRealisticRound([
                [
                    ...array_fill(0, 4, true),
                    false,
                    ...array_fill(0, 48, true),
                ],
            ], $this);
        }

        createPartialRound($round, $height, 51, $this, [$validators->get(4)->address], [$validators->get(4)->address]);

        expect((new WalletViewModel($validators->get(4)))->performance())->toBe([false, false]);

        $this->browse(function (Browser $browser) use ($resolution) {
            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('validator-monitor')
                ->waitForText(' / 53 Blocks', 10)
                ->assertEquals('[data-testid="validator-monitor:forging-count"] span', '52')
                ->assertEquals('[data-testid="validator-monitor:missed-count"] span', '0')
                ->assertEquals('[data-testid="validator-monitor:not-forging-count"] span', '1');
        });
    })->with('resolutions');

    it('should calculate not forging correctly for previous rounds', function ($resolution) {
        $this->travelTo(Carbon::parse('2024-02-01 14:00:00Z'));

        $this->freezeTime();

        [0 => $validators] = createRealisticRound([
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 48, true),
            ],
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 48, true),
            ],
            [
                ...array_fill(0, 4, true),
                false,
                ...array_fill(0, 48, true),
            ],
        ], $this);

        expect((new WalletViewModel($validators->get(4)))->performance())->toBe([false, false]);

        $this->browse(function (Browser $browser) use ($resolution) {
            $browser->resize($resolution['width'], $resolution['height']);

            $browser->visitRoute('validator-monitor')
                ->waitForText(' / 53 Blocks', 10)
                ->assertEquals('[data-testid="validator-monitor:forging-count"] span', '52')
                ->assertEquals('[data-testid="validator-monitor:missed-count"] span', '0')
                ->assertEquals('[data-testid="validator-monitor:not-forging-count"] span', '1');
        });
    })->with('resolutions');
});

dataset('resolutions', [
    'desktop' => [['width' => 1280, 'height' => 1024]],
    'lg' => [['width' => 1024, 'height' => 768]],
    'md-lg' => [['width' => 960, 'height' => 667]],
    'md' => [['width' => 768, 'height' => 1024]],
    'sm' => [['width' => 640, 'height' => 960]],
    'xs' => [['width' => 370, 'height' => 844]],
]);
