<?php

declare(strict_types=1);

namespace App\Services\Monitor;

use App\Facades\Network;
use App\Models\Block;
use App\Models\Scopes\OrderByHeightScope;
use Carbon\Carbon;

final class Slots
{
    public function getTime(?int $timestamp = null): int
    {
        if (is_null($timestamp)) {
            $timestamp = Carbon::now()->unix();
        } else {
            $timestamp = Carbon::createFromTimestamp($timestamp)->unix();
        }

        $start = Network::epoch()->unix();

        return $timestamp - $start;
    }

    public function getTimeInMsUntilNextSlot(): int
    {
        $nextSlotTime = $this->getSlotTime($this->getNextSlot());
        $now          = $this->getTime();

        return ($nextSlotTime - $now) * 1000;
    }

    public function getSlotNumber(?int $timestamp = null, ?int $height = null): int
    {
        if (is_null($timestamp)) {
            $timestamp = $this->getTime();
        }

        $latestHeight = $this->getLatestHeight($height);

        return $this->getSlotInfo($timestamp, $latestHeight)['slotNumber'];
    }

    public function getSlotTime(int $slot, ?int $height = null): int
    {
        $latestHeight = $this->getLatestHeight($height);

        return $this->calculateSlotTime($slot, $latestHeight);
    }

    public function getNextSlot(): int
    {
        return $this->getSlotNumber() + 1;
    }

    public function isForgingAllowed(?int $timestamp = null, ?int $height = null): bool
    {
        if (is_null($timestamp)) {
            $timestamp = $this->getTime();
        }

        $latestHeight = $this->getLatestHeight($height);

        return (bool) $this->getSlotInfo($timestamp, $latestHeight)['forgingStatus'];
    }

    public function getSlotInfo(?int $timestamp = null, ?int $height = null): array
    {
        if (is_null($timestamp)) {
            $timestamp = $this->getTime();
        }

        $blockTime               = Network::blockTime();
        $totalSlotsFromLastSpan  = 0;
        $lastSpanEndTime         = 0;

        $slotNumberUpUntilThisTimestamp = floor(($timestamp - $lastSpanEndTime) / $blockTime);
        $slotNumber                     = $totalSlotsFromLastSpan + $slotNumberUpUntilThisTimestamp;
        $startTime                      = $lastSpanEndTime + $slotNumberUpUntilThisTimestamp * $blockTime;
        $endTime                        = $startTime + $blockTime - 1;
        $forgingStatus                  = $timestamp < $startTime + floor($blockTime / 2);

        return [
            'blockTime'     => (int) $blockTime,
            'startTime'     => (int) $startTime,
            'endTime'       => (int) $endTime,
            'slotNumber'    => (int) $slotNumber,
            'forgingStatus' => (bool) $forgingStatus,
        ];
    }

    private function calculateSlotTime(int $slotNumber, int $height): int
    {
        $blockTime              = Network::blockTime();
        $totalSlotsFromLastSpan = 0;
        $milestoneHeight        = 1;
        $lastSpanEndTime        = 0;

        return $lastSpanEndTime + ($slotNumber - $totalSlotsFromLastSpan) * $blockTime;
    }

    private function getLatestHeight(?int $height): int
    {
        if (is_null($height)) {
            return Block::withScope(OrderByHeightScope::class)->firstOrFail()->height->toNumber();
        }

        return $height;
    }
}
