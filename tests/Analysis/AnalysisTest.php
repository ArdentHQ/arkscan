<?php

declare(strict_types=1);

namespace Tests\Analysis;

use GrahamCampbell\Analyzer\AnalysisTrait;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
final class AnalysisTest extends TestCase
{
    use AnalysisTrait;

    public static function getPaths(): array
    {
        return [
            __DIR__.'/../../app',
            __DIR__.'/../../tests',
        ];
    }

    public function getIgnored(): array
    {
        return [
            'InvalidModel',
            'Laravel\Scout\Builder',
            'Spatie\Snapshots\assertMatchesSnapshot',
            'Tests\bip39',
            'Tests\faker',
            'Tests\fakeCryptoCompare',
            'Tests\fakeKnownWallets',
            'Tests\createBlock',
            'Tests\createRoundEntry',
            'Tests\createRealisticRound',
            'Tests\createFullRound',
            'Tests\createPartialRound',
            'Tests\delegatesForRound',
            'Tests\getDelegateForgingPosition',
        ];
    }
}
