<?php

declare(strict_types=1);

namespace App\Events;

class NewWalletBlock extends NewEntity
{
    const CHANNEL = 'wallet-blocks';
}
