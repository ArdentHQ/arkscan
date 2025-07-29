<?php

declare(strict_types=1);

namespace App\Enums;

enum WebhookEvents: string
{
    case BlockApplied = 'block.applied';

    case TransactionApplied = 'transaction.applied';

    case WalletVote = 'wallet.vote';
}
