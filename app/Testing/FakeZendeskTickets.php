<?php

declare(strict_types=1);

namespace App\Testing;

final class FakeZendeskTickets
{
    public function create(array $params): object
    {
        return (object) ['id' => 1];
    }
}
