<?php

declare(strict_types=1);

namespace App\Events;

final class CurrencyUpdate extends NewEntity
{
    public const CHANNEL = 'currency-update';
}
