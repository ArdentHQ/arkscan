<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\RoundRepository as Contract;
use App\Facades\Rounds;
use App\Models\Block;
use App\Models\Round;
use App\Services\Monitor\Monitor;
use App\Services\Monitor\ValidatorTracker;
use Illuminate\Support\Collection as SupportCollection;

final class RoundRepository implements Contract
{
    public function current(): Round
    {
        return Round::orderBy('round', 'desc')->firstOrFail();
    }

    public function byRound(int $round): Round
    {
        return Round::findOrFail($round);
    }

    public function validators(bool $withBlock = true): SupportCollection
    {
        $round       = Rounds::current();
        $roundNumber = $round->round;
        $validators  = Rounds::byRound($roundNumber)->validators;
        $heightRange = Monitor::heightRangeByRound($round);
        $validators  = new SupportCollection(ValidatorTracker::execute($validators, $heightRange[0]));

        if ($withBlock) {
            $blocks = Block::whereBetween('height', $heightRange)->get()->keyBy('generator_address');

            $validators = $validators->map(fn ($validator) => [
                ...$validator,

                'block' => $blocks->get($validator['address']),
            ]);
        }

        return $validators;
    }
}
