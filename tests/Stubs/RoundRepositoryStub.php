<?php

declare(strict_types=1);

namespace Tests\Stubs;

use App\Contracts\RoundRepository;
use App\Models\Round;
use Illuminate\Support\Collection as SupportCollection;

class RoundRepositoryStub implements RoundRepository
{
    public int $currentCalls = 0;
    public int $byRoundCalls = 0;
    public int $validatorsCalls = 0;

    public function __construct(private Round $round, private SupportCollection $validators)
    {
    }

    public function current(): Round
    {
        $this->currentCalls++;

        return $this->round;
    }

    public function byRound(int $round): Round
    {
        $this->byRoundCalls++;

        return $this->round;
    }

    public function validators(bool $withBlock = true): SupportCollection
    {
        $this->validatorsCalls++;

        return $this->validators;
    }
}
