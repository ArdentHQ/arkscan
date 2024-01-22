<?php

declare(strict_types=1);

namespace App\Events;

class NewWalletBlock extends NewEntity
{
    public const CHANNEL = 'wallet-blocks';
}
