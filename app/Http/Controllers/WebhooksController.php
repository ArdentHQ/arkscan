<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\WebhookEvents;
use App\Http\Controllers\Concerns\HandlesBlockWebhooks;
use App\Http\Controllers\Concerns\HandlesTransactionWebhooks;
use App\Http\Controllers\Concerns\HandlesWalletWebhooks;

final class WebhooksController
{
    use HandlesBlockWebhooks;
    use HandlesTransactionWebhooks;
    use HandlesWalletWebhooks;

    public function __invoke(): void
    {
        // @phpstan-ignore-next-line
        if (! request()->hasValidSignature()) {
            abort(401);
        }

        $event = request()->input('event');

        if ($event === WebhookEvents::BlockApplied) {
            $this->handleBlockApplied();
            $this->handleGeneratorBlockApplied();

            return;
        }

        if ($event === WebhookEvents::TransactionApplied) {
            $this->handleTransactionApplied();
            $this->handleSenderTransactionApplied();
            $this->handleRecipientTransactionApplied();

            return;
        }

        if ($event === WebhookEvents::WalletVote) {
            $this->handleWalletVote();
            $this->handleWalletUnvote();

            return;
        }
    }
}
