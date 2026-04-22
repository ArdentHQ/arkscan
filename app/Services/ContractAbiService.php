<?php

declare(strict_types=1);

namespace App\Services;

use ArkEcosystem\Crypto\Enums\ContractAbiType;
use Illuminate\Support\Facades\Cache;
use kornrunner\Keccak;

final class ContractAbiService
{
    public const CACHE_KEY = 'contract_abi_signatures';

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
     * Maps config logical names to ABI function names.
     *
     * @var array<string, string>
     */
    private const KNOWN_METHOD_MAP = [
        'vote'                   => 'vote',
        'unvote'                 => 'unvote',
        'validator_registration' => 'registerValidator',
        'validator_resignation'  => 'resignValidator',
        'validator_update'       => 'updateValidator',
        'multipayment'           => 'pay',
        'username_registration'  => 'registerUsername',
        'username_resignation'   => 'resignUsername',
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
     * In-memory cache for known method hashes (avoids re-reading ABIs per call).
     *
     * @var array<string, string>|null
     */
    private ?array $knownMethodHashesCache = null;

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
        return $this->getKnownMethodHashes()[$name] ?? null;
    }

    /**
     * Get all known method hashes (logicalName => hash).
     *
     * @return array<string, string>
     */
    public function getKnownMethodHashes(): array
    {
        if ($this->knownMethodHashesCache !== null) {
            return $this->knownMethodHashesCache;
        }

        $hashes     = self::STATIC_METHOD_HASHES;
        $signatures = $this->getAllSignatures();

        foreach (self::KNOWN_METHOD_MAP as $name => $functionName) {
            foreach ($signatures as $hash => $sig) {
                if (str_starts_with($sig, $functionName.'(')) {
                    $hashes[$name] = $hash;

                    break;
                }
            }
        }

        $this->knownMethodHashesCache = $hashes;

        return $hashes;
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
        $fileName = (new \ReflectionClass(\ArkEcosystem\Crypto\Utils\AbiBase::class))->getFileName();

        if ($fileName === false) {
            throw new \RuntimeException('Could not resolve file path for ArkEcosystem\\Crypto\\Utils\\AbiBase.'); // @codeCoverageIgnore
        }

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
