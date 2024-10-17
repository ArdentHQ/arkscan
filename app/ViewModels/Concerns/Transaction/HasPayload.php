<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

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
        $payload = $this->rawPayload();
        if ($payload === null) {
            return null;
        }

        $methodId = substr($payload, 0, 8);

        return trans('contracts.formatted', [
            'function'  => trans('contracts.'.$methodId),
            'methodId'  => $methodId,
            'arguments' => $this->payloadArguments()?->map(fn ($argument, $index) => trans('contracts.argument', [
                'index' => $index,
                'value' => $argument,
            ]))->implode("\n") ?? '',
        ]);
    }

    private function payloadArguments(): ?Collection
    {
        $payload = $this->rawPayload();
        if ($payload === null) {
            return null;
        }

        $argumentsPayload = substr($payload, 8);
        $arguments        = collect(explode(' ', trim(chunk_split($argumentsPayload, 64, ' '))));

        return collect($arguments);
    }
}
