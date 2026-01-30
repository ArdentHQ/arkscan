<?php

declare(strict_types=1);

namespace App\Testing;

final class FakeZendesk
{
    public function tickets(): FakeZendeskTickets
    {
        return new FakeZendeskTickets();
    }
}
