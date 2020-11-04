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
        self::BASE58_ADDRESS_P2PKH => '17',
        self::BASE58_ADDRESS_P2SH  => '00',
        self::BASE58_WIF           => 'aa',
    ];

    /**
     * {@inheritdoc}
     *
     * @see Network::$bip32PrefixMap
     */
    protected $bip32PrefixMap = [
        self::BIP32_PREFIX_XPUB => '46090600',
        self::BIP32_PREFIX_XPRV => '46089520',
    ];

    private string $epoch;

    public function __construct(array $config)
    {
        parent::__construct();

        $this->base58PrefixMap[self::BASE58_ADDRESS_P2PKH] = dechex($config['base58Prefix']);
        $this->epoch                                       = $config['epoch'];
    }

    /**
     * {@inheritdoc}
     */
    public function pubKeyHash(): int
    {
        return intval($this->base58PrefixMap[self::BASE58_ADDRESS_P2PKH]);
    }

    /**
     * {@inheritdoc}
     */
    public function epoch(): string
    {
        return $this->epoch;
    }
}
