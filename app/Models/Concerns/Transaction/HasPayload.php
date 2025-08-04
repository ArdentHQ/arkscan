<?php

declare(strict_types=1);

namespace App\Models\Concerns\Transaction;

use App\Enums\ContractMethod;
use App\Services\BigNumber;
use ArkEcosystem\Crypto\Enums\ContractAbiType;
use ArkEcosystem\Crypto\Utils\AbiDecoder;
use ArkEcosystem\Crypto\Utils\UnitConverter;
use Brick\Math\RoundingMode;

trait HasPayload
{
    public function hasPayload(): bool
    {
        return $this->rawPayload() !== null;
    }

    public function rawPayload(): ?string
    {
        return $this->decodePayload($this->data);
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

    /**
     * @return array<int, array{address: string, amount: float}>
     */
    public function multiPaymentRecipients(): array
    {
        /**
         * @var string $payload
         */
        $payload = $this->rawPayload();

        if (! str_starts_with($payload, ContractMethod::multiPayment())) {
            throw new \Exception('This transaction is not a multi-payment.');
        }

        $method = (new AbiDecoder(ContractAbiType::MULTIPAYMENT))->decodeFunctionData($payload);

        $recipients = [];

        $addresses = $method['args'][0];
        $amounts   = $method['args'][1];

        foreach ($addresses as $index => $address) {
            if (isset($amounts[$index])) {
                $recipients[] = [
                    'address' => $address,
                    'amount'  => UnitConverter::formatUnits($amounts[$index], 'ark'),
                ];
            }
        }

        return $recipients;
    }

    public function getMethodData(bool $tryAllAbis = false): ?array
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
            if ($tryAllAbis) {
                $method = $this->decodeFunctionData($payload);
            } else {
                $method = (new AbiDecoder())->decodeFunctionData($payload);
            }

            // @codeCoverageIgnoreStart
            // Unreachable on tests as all the methods in the `AbiDecoder` class
            // are covered. Still neccesary in case of future changes.
            if ($functionName === null) {
                $functionName = $method['functionName'];
            }
            // @codeCoverageIgnoreEnd

            $arguments = $method['args'];
        } catch (\Throwable) {
            $arguments = $this->payloadArguments($payload);
        }

        return [$functionName, $methodId, $arguments];
    }

    public function parseReceiptError(): ?string
    {
        // Code-wise, receipt could be null, but in practice it should never be.
        if ($this->receipt === null) {
            return null;
        }

        $outputPayload = $this->decodePayload($this->receipt->output);
        if ($outputPayload !== null) {
            $contractAbiTypes = [
                ContractAbiType::CUSTOM,
                ContractAbiType::CONSENSUS,
                ContractAbiType::MULTIPAYMENT,
                ContractAbiType::USERNAMES,
            ];

            foreach ($contractAbiTypes as $type) {
                try {
                    return (new AbiDecoder($type))->decodeError($outputPayload);
                } catch (\Throwable $e) {
                    // If the ABI type is not found, we will try the next one
                }
            }
        }

        $insufficientGasThreshold = config('arkscan.transaction.insufficient_gas_threshold', 0.95);
        $gasUsed                  = BigNumber::new($this->receipt->gas_used->valueOf());
        if ($gasUsed->dividedBy($this->gas, 2, RoundingMode::DOWN)->valueOf()->toFloat() > $insufficientGasThreshold) {
            return 'InsufficientGas';
        }

        return null;
    }

    /**
     * @param resource|null $payload
     */
    private function decodePayload($payload): ?string
    {
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

    private function decodeFunctionData(string $payload): array
    {
        $contractAbiTypes = [
            ContractAbiType::CUSTOM,
            ContractAbiType::CONSENSUS,
            ContractAbiType::MULTIPAYMENT,
            ContractAbiType::USERNAMES,
        ];

        foreach ($contractAbiTypes as $type) {
            try {
                return (new AbiDecoder($type))->decodeFunctionData($payload);
            } catch (\Throwable $e) {
                // If the ABI type is not found, we will try the next one
            }
        }

        throw new \Exception('Unable to decode function data from payload.');
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
