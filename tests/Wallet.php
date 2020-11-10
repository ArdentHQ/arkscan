<?php

declare(strict_types=1);

namespace Tests;

use Faker\Generator;
use Faker\Provider\Base;
use Illuminate\Support\Collection;

final class Wallet extends Base
{
    private static $wallets;

    private Collection $availableWallets;

    public function __construct(Generator $generator)
    {
        parent::__construct($generator);

        if (self::$wallets === null) {
            self::$wallets = json_decode(file_get_contents(base_path('tests/fixtures/wallets.json')), true);
        }
        $this->availableWallets = collect(self::$wallets)->shuffle();
    }

    public function wallet()
    {
        return $this->availableWallets->shift();
    }
}
