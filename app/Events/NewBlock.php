<?php

declare(strict_types=1);

namespace App\Events;

final class NewBlock extends NewEntity
{
    public const CHANNEL = 'blocks';
}
