<?php

declare(strict_types=1);

namespace App\Events;

class NewBlock extends NewEntity
{
    const CHANNEL = 'blocks';
}
