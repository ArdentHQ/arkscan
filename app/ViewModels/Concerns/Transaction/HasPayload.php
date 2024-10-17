<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use Illuminate\Support\Arr;

trait HasPayload
{
    public function hasPayload(): bool
    {
        return $this->rawPayload() !== null;
    }

    public function rawPayload(): ?string
    {
        $payload = Arr::get($this->transaction, 'asset.evmCall.payload');
        if (is_string($payload) && strlen($payload) === 0) {
            return null;
        }

        return $payload;
    }

    public function utf8Payload(): ?string
    {
        $payload = $this->rawPayload();
        if ($payload === null) {
            return null;
        }

        $utf8 = hex2bin($payload);
        if ($utf8 === false) {
            return null;
        }

        return $utf8;
    }

    public function formattedPayload(): ?string
    {
        return $this->rawPayload();
    }
}
