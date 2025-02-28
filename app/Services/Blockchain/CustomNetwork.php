<?php

declare(strict_types=1);

namespace App\Services\Blockchain;

use ArkEcosystem\Crypto\Networks\AbstractNetwork;

final class CustomNetwork extends AbstractNetwork
{
    /**
     * {@inheritdoc}
     *
     * @see Network::$base58PrefixMap
     */
    protected $base58PrefixMap = [
        self::BASE58_WIF => 'ba', // 186
    ];

    private string $epoch;

    public function __construct(array $config)
    {
        parent::__construct();

        $this->base58PrefixMap[self::BASE58_ADDRESS_P2PKH] = dechex($config['base58Prefix']);
        $this->epoch                                       = $config['epoch'];
    }

    /**
     * Get the chain identifier.
     *
     * @return int
     */
    public function chainId(): int
    {
        return 10000;
    }

    /**
     * {@inheritdoc}
     */
    public function epoch(): string
    {
        return $this->epoch;
    }
}
