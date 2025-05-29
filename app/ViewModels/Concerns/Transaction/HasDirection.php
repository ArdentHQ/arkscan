<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

trait HasDirection
{
    public function isSent(string $address): bool
    {
        return $this->direction->isSent($address);
    }

    public function isSentToSelf(string $address): bool
    {
        if (! $this->isTransfer() && ! $this->isTokenTransfer() && ! $this->isMultiPayment()) {
            return false;
        }

        if ($this->sender() !== null && $address !== $this->sender()->address) {
            return false;
        }

        if (! $this->isMultiPayment() && $address !== $this->recipient()?->address) {
            return false;
        }

        if ($this->isMultiPayment()) {
            $recipients = $this->multiPaymentRecipients();
            foreach ($recipients as $recipient) {
                if ($recipient['address'] === $address) {
                    return true;
                }
            }

            return false;
        }

        return true;
    }

    public function isReceived(string $address): bool
    {
        return $this->direction->isReceived($address);
    }
}
