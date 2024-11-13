<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

trait HasPayload
{
    public function hasPayload(): bool
    {
        return $this->rawPayload() !== null;
    }

    public function rawPayload(): ?string
    {
        $payload = $this->transaction->data;
        if ($payload === null) {
            return null;
        }

        $payload = bin2hex(stream_get_contents($payload, offset: 0));
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

    public function methodHash(): ?string
    {
        $payload = $this->rawPayload();
        if ($payload === null) {
            return null;
        }

        return substr($payload, 0, 8);
    }

    public function formattedPayload(): ?string
    {
        $methodId = $this->methodHash();

        $functionName = null;
        if (app('translator')->has('contracts.'.$methodId)) {
            $functionName = trans('contracts.'.$methodId);
        }

        return trim(view('components.transaction.code-block.formatted-contract', [
            'function'  => $functionName,
            'methodId'  => $methodId,
            'arguments' => $this->payloadArguments(),
        ])->render());
    }

    private function payloadArguments(): ?array
    {
        $payload = $this->rawPayload();
        if ($payload === null) {
            return [];
        }

        $argumentsPayload   = substr($payload, 8);
        $separatedArguments = trim(chunk_split($argumentsPayload, 64, ' '));
        if (strlen($separatedArguments) === 0) {
            return [];
        }

        return explode(' ', $separatedArguments);
    }
}
