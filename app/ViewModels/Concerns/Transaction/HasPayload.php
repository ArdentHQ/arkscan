<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use ArkEcosystem\Crypto\Utils\AbiDecoder;

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

        
        $payloadContent = stream_get_contents($payload, offset: 0);
        // @codeCoverageIgnoreStart
        // Not covered in tests, since it seems that the possibility of returning
        // `false` instead of an exception seems to depends on the PHP configuration.
        if ($payloadContent === false) {
            return null;
        }
        // @codeCoverageIgnoreEnd

        $payload = bin2hex($payloadContent);
        if (strlen($payload) === 0) {
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

        // @codeCoverageIgnoreStart
        // Not covered in tests, since it seems that the possibility of returning
        // `false` instead of an exception seems to depends on the PHP configuration.
        $utf8 = hex2bin($payload);
        if ($utf8 === false) {
            return null;
        }
        // @codeCoverageIgnoreEnd

        return $utf8;
    }

    public function methodHash(?string $payload = null): ?string
    {
        if ($payload === null) {
            $payload = $this->rawPayload();
            if ($payload === null) {
                return null;
            }
        }

        return substr($payload, 0, 8);
    }

    public function formattedPayload(): ?string
    {
        $methodData = $this->getMethodData();
        if ($methodData === null) {
            return null;
        }

        [$functionName, $methodId, $arguments] = $methodData;

        return trim(view('components.transaction.code-block.formatted-contract', [
            'function'  => $functionName,
            'methodId'  => $methodId,
            'arguments' => $arguments,
        ])->render());
    }

    private function getMethodData(): ?array
    {
        $payload = $this->rawPayload();
        if ($payload === null) {
            return null;
        }

        $methodId = $this->methodHash($payload);

        $functionName = null;
        if (app('translator')->has('contracts.'.$methodId)) {
            $functionName = trans('contracts.'.$methodId);
        }

        try {
            $method = (new AbiDecoder())->decodeFunctionData($payload);
            if ($functionName === null) {
                $functionName = $method['functionName']; // @cover
            }

            $arguments = $method['args'];
        } catch (\Throwable) {
            $arguments = $this->payloadArguments($payload);
        }

        return [$functionName, $methodId, $arguments];
    }

    private function payloadArguments(string $payload): ?array
    {
        $argumentsPayload   = substr($payload, 8);
        $separatedArguments = trim(chunk_split($argumentsPayload, 64, ' '));
        if (strlen($separatedArguments) === 0) {
            return [];
        }

        return explode(' ', $separatedArguments);
    }
}
