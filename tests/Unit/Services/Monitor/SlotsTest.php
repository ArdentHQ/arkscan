<?php

declare(strict_types=1);

use App\Models\Block;
use App\Services\Monitor\Slots;

beforeEach(function () {
    Block::factory()->create();

    $this->subject = new Slots();
});

it('return epoch time as number', function () {
    $result = $this->subject->getTime(1603703705);

    expect($result)->toBeNumeric();
    expect($result)->toEqual(113602505);
});

it('return slot number', function () {
    expect($this->subject->getSlotNumber(1, 1))->toBe(0);
    expect($this->subject->getSlotNumber(4, 1))->toBe(0);
    expect($this->subject->getSlotNumber(7, 1))->toBe(0);

    expect($this->subject->getSlotNumber(8, 2))->toBe(1);
    expect($this->subject->getSlotNumber(9, 2))->toBe(1);
    expect($this->subject->getSlotNumber(10, 2))->toBe(1);
    expect($this->subject->getSlotNumber(11, 2))->toBe(1);
    expect($this->subject->getSlotNumber(15, 2))->toBe(1);

    expect($this->subject->getSlotNumber(16, 3))->toBe(2);
    expect($this->subject->getSlotNumber(20, 3))->toBe(2);

    expect($this->subject->getSlotNumber(24, 4))->toBe(3);

    expect($this->subject->getSlotNumber(8000, 1001))->toBe(1000);

    expect($this->subject->getSlotNumber(15000, 1876))->toBe(1875);

    expect($this->subject->getSlotNumber(169000, 21126))->toBe(21125);
    expect($this->subject->getSlotNumber(169001, 21126))->toBe(21125);
    expect($this->subject->getSlotNumber(169005, 21126))->toBe(21125);
    expect($this->subject->getSlotNumber(169007, 21126))->toBe(21125);
});

it('returns slot time', function () {
    expect($this->subject->getSlotTime(1, 2))->toBe(8);
    expect($this->subject->getSlotTime(8, 9))->toBe(64);
    expect($this->subject->getSlotTime(50, 51))->toBe(400);
    expect($this->subject->getSlotTime(8888, 8889))->toBe(71104);
    expect($this->subject->getSlotTime(19614, 19615))->toBe(156912);
    expect($this->subject->getSlotTime(19700, 19701))->toBe(157600);
    expect($this->subject->getSlotTime(169000, 1))->toBe(1352000);
});

it('[getSlotInfo] should return positive values when called without timestamp', function () {
    $slotInfo = $this->subject->getSlotInfo();

    expect($slotInfo['startTime'])->toBeNumeric();
    expect($slotInfo['endTime'])->toBeNumeric();
    expect($slotInfo['blockTime'])->toBeNumeric();
    expect($slotInfo['slotNumber'])->toBeNumeric();
    expect($slotInfo['forgingStatus'])->toBeBool();
});

it('should return correct values', function () {
    $expectedResults = [
        ['height' => 1, 'timestamp' => 0, 'startTime' => 0, 'endTime' => 7, 'blockTime' => 8, 'slotNumber' => 0,  'forgingStatus'=> true],
        ['height' => 2, 'timestamp' => 8, 'startTime' => 8, 'endTime' => 15, 'blockTime' => 8, 'slotNumber' => 1,  'forgingStatus'=> true],
        ['height' => 3, 'timestamp' => 16, 'startTime' => 16, 'endTime' => 23, 'blockTime' => 8, 'slotNumber' => 2,  'forgingStatus'=> true],
        ['height' => 4, 'timestamp' => 24, 'startTime' => 24, 'endTime' => 31, 'blockTime' => 8, 'slotNumber' => 3,  'forgingStatus'=> true],

        ['height' => 4, 'timestamp' => 25, 'startTime' => 24, 'endTime' => 31, 'blockTime' => 8, 'slotNumber' => 3,  'forgingStatus'=> true],
        ['height' => 4, 'timestamp' => 26, 'startTime' => 24, 'endTime' => 31, 'blockTime' => 8, 'slotNumber' => 3,  'forgingStatus'=> true],
        ['height' => 4, 'timestamp' => 27, 'startTime' => 24, 'endTime' => 31, 'blockTime' => 8, 'slotNumber' => 3,  'forgingStatus'=> true],
    ];

    $endSlotTimeResults = [
        ['height' => 1, 'timestamp' => 7, 'startTime' => 0, 'endTime' => 7, 'blockTime' => 8, 'slotNumber' => 0,  'forgingStatus'=> false],
        ['height' => 2, 'timestamp' => 15, 'startTime' => 8, 'endTime' => 15, 'blockTime' => 8, 'slotNumber' => 1,  'forgingStatus'=> false],
        ['height' => 3, 'timestamp' => 23, 'startTime' => 16, 'endTime' => 23, 'blockTime' => 8, 'slotNumber' => 2,  'forgingStatus'=> false],
        ['height' => 4, 'timestamp' => 31, 'startTime' => 24, 'endTime' => 31, 'blockTime' => 8, 'slotNumber' => 3,  'forgingStatus'=> false],

        ['height' => 4, 'timestamp' => 30, 'startTime' => 24, 'endTime' => 31, 'blockTime' => 8, 'slotNumber' => 3,  'forgingStatus'=> false],
        ['height' => 4, 'timestamp' => 29, 'startTime' => 24, 'endTime' => 31, 'blockTime' => 8, 'slotNumber' => 3,  'forgingStatus'=> false],
        ['height' => 4, 'timestamp' => 28, 'startTime' => 24, 'endTime' => 31, 'blockTime' => 8, 'slotNumber' => 3,  'forgingStatus'=> false],
    ];

    $missedBlocks = [
        ['height' => 2, 'timestamp' => 24, 'startTime' => 24, 'endTime' => 31, 'blockTime' => 8, 'slotNumber' => 3,  'forgingStatus'=> true],
        ['height' => 2, 'timestamp' => 31, 'startTime' => 24, 'endTime' => 31, 'blockTime' => 8, 'slotNumber' => 3,  'forgingStatus'=> false],
    ];

    $expectations = array_merge($expectedResults, $endSlotTimeResults, $missedBlocks);

    foreach ($expectations as  $expectation) {
        expect($this->subject->getSlotInfo($expectation['timestamp'], $expectation['height']))->toEqual([
            'startTime'     => $expectation['startTime'],
            'endTime'       => $expectation['endTime'],
            'blockTime'     => $expectation['blockTime'],
            'slotNumber'    => $expectation['slotNumber'],
            'forgingStatus' => $expectation['forgingStatus'],
        ]);
    }
});

it('[getNextSlot] returns next slot', function () {
    expect($this->subject->getNextSlot())->toBeNumeric();
});

it('[getNextSlot] returns next when height is defined in configManager', function () {
    Block::factory()->create(['height' => 12]);

    expect($this->subject->getNextSlot())->toBeNumeric();
});

it('[isForgingAllowed] returns boolean', function () {
    expect($this->subject->isForgingAllowed())->toBeBool();
});

it('[isForgingAllowed] returns true when over half the time in the block remains', function () {
    expect($this->subject->isForgingAllowed(0))->toBeTrue();
    expect($this->subject->isForgingAllowed(1))->toBeTrue();
    expect($this->subject->isForgingAllowed(3))->toBeTrue();
    expect($this->subject->isForgingAllowed(8))->toBeTrue();
    expect($this->subject->isForgingAllowed(16))->toBeTrue();
});

it('[isForgingAllowed] returns false when under half the time in the block remains', function () {
    expect($this->subject->isForgingAllowed(4))->toBeFalse();
    expect($this->subject->isForgingAllowed(5))->toBeFalse();
    expect($this->subject->isForgingAllowed(6))->toBeFalse();
    expect($this->subject->isForgingAllowed(7))->toBeFalse();
    expect($this->subject->isForgingAllowed(15))->toBeFalse();
});

it('should get the time in ms until next slot', function () {
    Block::factory()->create(['height' => 1]);

    $nextSlotTime = $this->subject->getSlotTime($this->subject->getNextSlot());
    $now          = $this->subject->getTime();

    expect($this->subject->getTimeInMsUntilNextSlot())->toEqual(($nextSlotTime - $now) * 1000);
});
