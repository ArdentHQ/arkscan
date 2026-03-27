<?php

declare(strict_types=1);

namespace App\Services;

use ArkEcosystem\Crypto\Enums\ContractAbiType;
use Illuminate\Support\Facades\Cache;
use kornrunner\Keccak;

final class ContractAbiService
{
    private const CACHE_KEY = 'contract_abi_signatures';

    /**
     * ERC-20 standard method signatures not included in Mainsail ABIs.
     *
     * @var array<string, string>
     */
    private const ERC20_SIGNATURES = [
        'a9059cbb' => 'transfer(address,uint256)',
        '23b872dd' => 'transferFrom(address,address,uint256)',
        '095ea7b3' => 'approve(address,uint256)',
        '70a08231' => 'balanceOf(address)',
        '313ce567' => 'decimals()',
        '18160ddd' => 'totalSupply()',
        '06fdde03' => 'name()',
        '95d89b41' => 'symbol()',
        'dd62ed3e' => 'allowance(address,address)',
    ];

    /**
     * Maps config logical names to [ContractAbiType, functionName].
     * Used to derive known method hashes from ABIs.
     *
     * @var array<string, array{0: ContractAbiType, 1: string}>
     */
    private const KNOWN_METHOD_MAP = [
        'vote'                   => [ContractAbiType::CONSENSUS, 'vote'],
        'unvote'                 => [ContractAbiType::CONSENSUS, 'unvote'],
        'validator_registration' => [ContractAbiType::CONSENSUS, 'registerValidator'],
        'validator_resignation'  => [ContractAbiType::CONSENSUS, 'resignValidator'],
        'validator_update'       => [ContractAbiType::CONSENSUS, 'updateValidator'],
        'multipayment'           => [ContractAbiType::MULTIPAYMENT, 'pay'],
        'username_registration'  => [ContractAbiType::USERNAMES, 'registerUsername'],
        'username_resignation'   => [ContractAbiType::USERNAMES, 'resignUsername'],
    ];

    /**
     * Known method hashes for methods not in any ABI.
     *
     * @var array<string, string>
     */
    private const STATIC_METHOD_HASHES = [
        'transfer'            => 'a9059cbb',
        'approve'             => '095ea7b3',
        'contract_deployment' => '60806040',
        'batch_transfer'      => '4885b254',
    ];

    /**
     * Get all method signatures (hash => signature) from ABIs and ERC-20 standards.
     *
     * @return array<string, string>
     */
    public function getAllSignatures(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, fn () => $this->buildSignatureMap());
    }

    /**
     * Get the function signature for a given method hash.
     */
    public function getSignature(string $hash): ?string
    {
        return $this->getAllSignatures()[$hash] ?? null;
    }

    /**
     * Get the method hash for a known contract method by its config logical name.
     */
    public function getKnownMethodHash(string $name): ?string
    {
        if (array_key_exists($name, self::STATIC_METHOD_HASHES)) {
            return self::STATIC_METHOD_HASHES[$name];
        }

        if (! array_key_exists($name, self::KNOWN_METHOD_MAP)) {
            return null;
        }

        [$abiType, $functionName] = self::KNOWN_METHOD_MAP[$name];

        return $this->getFunctionHash($functionName, $abiType);
    }

    /**
     * Get all known method hashes (logicalName => hash).
     *
     * @return array<string, string>
     */
    public function getKnownMethodHashes(): array
    {
        $hashes = self::STATIC_METHOD_HASHES;

        foreach (self::KNOWN_METHOD_MAP as $name => $mapping) {
            [$abiType, $functionName] = $mapping;
            $hash                     = $this->getFunctionHash($functionName, $abiType);

            if ($hash !== null) {
                $hashes[$name] = $hash;
            }
        }

        return $hashes;
    }

    /**
     * Build and cache the signature map. Also returns it.
     */
    public function cache(): void
    {
        Cache::forever(self::CACHE_KEY, $this->buildSignatureMap());
    }

    /**
     * Clear the cached signature map.
     */
    public function clear(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Get the 4-byte selector hash for a function name in a specific ABI.
     */
    private function getFunctionHash(string $functionName, ContractAbiType $abiType): ?string
    {
        $abi = $this->loadAbi($abiType);

        foreach ($abi as $item) {
            if (($item['type'] ?? null) !== 'function') {
                continue;
            }

            if ($item['name'] !== $functionName) {
                continue;
            }

            return $this->computeSelector($item);
        }

        return null;
    }

    /**
     * @return array<string, string>
     */
    private function buildSignatureMap(): array
    {
        $signatures = self::ERC20_SIGNATURES;

        $abiTypes = [
            ContractAbiType::CONSENSUS,
            ContractAbiType::MULTIPAYMENT,
            ContractAbiType::USERNAMES,
        ];

        foreach ($abiTypes as $type) {
            $abi = $this->loadAbi($type);

            foreach ($abi as $item) {
                if (($item['type'] ?? null) !== 'function') {
                    continue;
                }

                $signature = $this->getFunctionSignature($item);
                $hash      = $this->computeSelector($item);

                $signatures[$hash] = $signature;
            }
        }

        return $signatures;
    }

    private function loadAbi(ContractAbiType $type): array
    {
        $path = $this->abiPath($type);

        /** @var string $json */
        $json = file_get_contents($path);

        return json_decode($json, true)['abi'];
    }

    private function abiPath(ContractAbiType $type): string
    {
        /** @var string $fileName */
        $fileName = (new \ReflectionClass(\ArkEcosystem\Crypto\Utils\AbiBase::class))->getFileName();
        $basePath = dirname($fileName);

        return match ($type) {
            ContractAbiType::CONSENSUS    => $basePath.'/Abi/json/Abi.Consensus.json',
            ContractAbiType::MULTIPAYMENT => $basePath.'/Abi/json/Abi.Multipayment.json',
            ContractAbiType::USERNAMES    => $basePath.'/Abi/json/Abi.Usernames.json',
            default                       => throw new \InvalidArgumentException("Unsupported ABI type: {$type->value}"),
        };
    }

    private function getFunctionSignature(array $abiItem): string
    {
        $types = array_map(fn (array $input) => $input['type'], $abiItem['inputs']);

        return $abiItem['name'].'('.implode(',', $types).')';
    }

    private function computeSelector(array $abiItem): string
    {
        $signature = $this->getFunctionSignature($abiItem);

        return substr(Keccak::hash($signature, 256), 0, 8);
    }
}
