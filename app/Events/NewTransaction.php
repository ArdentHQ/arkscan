<?php

declare(strict_types=1);

namespace App\Events;

final class NewTransaction extends NewEntity
{
    public const CHANNEL = 'transactions';
}
