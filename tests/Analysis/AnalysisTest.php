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

    public function getPaths(): array
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
            'Tests\fakeCryptoCompare',
            'Tests\fakeKnownWallets',
        ];
    }
}
