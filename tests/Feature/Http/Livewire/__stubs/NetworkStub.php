<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Livewire\__stubs;

use Carbon\Carbon;

/**
 * @coversNothing
 */
class NetworkStub
{
    public function __construct(public bool $canBeExchanged, public ?Carbon $epoch = null)
    {
        //
    }

    public function canBeExchanged(): bool
    {
        return $this->canBeExchanged;
    }

    public function currency(): string
    {
        return 'DARK';
    }

    public function epoch(): ?Carbon
    {
        return $this->epoch;
    }
}
