<?php

declare(strict_types=1);

namespace App\Services\Monitor;

use App\Facades\Network;
use App\Models\Block;
use Illuminate\Support\Collection;

final class DelegateTracker
{
    public static function execute(Collection $delegates): array
    {
        // Arrange
        $lastBlock = Block::current();
        $height    = $lastBlock->height->toNumber();
        $timestamp = $lastBlock->timestamp;

        // Act
        $maxDelegates    = Network::delegateCount();
        $blockTime       = Network::blockTime();
        // $round           = RoundCalculator::calculate($height);
        $activeDelegates = $delegates->toBase()->map(fn ($delegate) => $delegate->public_key);
        // $blockTimeLookup = (new ForgingInfoCalculator())->getBlockTimeLookup($lastBlock->height->toNumber());
        $forgingInfo     = (new ForgingInfoCalculator())->calculateForgingInfo($timestamp, $height);

        // Determine Next Forgers...
        $nextForgers = [];
        for ($i = 0; $i < $maxDelegates; $i++) {
            $delegate = $activeDelegates[($forgingInfo['currentForger'] + $i) % $maxDelegates];

            if ($delegate) {
                $nextForgers[] = $delegate;
            }
        }

        if (count($activeDelegates) < $maxDelegates) {
            return [];
        }

        // Map Next Forgers...
        $result = [
            'delegates'     => [],
            'nextRoundTime' => ($maxDelegates - $forgingInfo['currentForger'] - 1) * $blockTime,
        ];

        foreach ($delegates as $delegate) {
            $indexInNextForgers = 0;

            for ($i = 0; $i < count($nextForgers); $i++) {
                if ($nextForgers[$i] === $delegate->public_key) {
                    $indexInNextForgers = $i;

                    break;
                }
            }

            if ($indexInNextForgers === 0) {
                $result['delegates'][$indexInNextForgers] = [
                    'publicKey' => $delegate->public_key,
                    'status'    => 'next',
                    'time'      => 0,
                ];
            } elseif ($indexInNextForgers <= $maxDelegates - $forgingInfo['nextForger']) {
                $result['delegates'][$indexInNextForgers] = [
                    'publicKey' => $delegate->public_key,
                    'status'    => 'pending',
                    'time'      => $indexInNextForgers * $blockTime * 1000,
                ];
            } else {
                $result['delegates'][$indexInNextForgers] = [
                    'publicKey' => $delegate->public_key,
                    'status'    => 'done',
                    'time'      => 0,
                ];
            }
        }

        return $result;
    }
}
