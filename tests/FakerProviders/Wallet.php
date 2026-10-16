<?php

declare(strict_types=1);

namespace Tests\FakerProviders;

use Faker\Generator;
use Faker\Provider\Base;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Random\Engine\Mt19937;
use Random\Randomizer;

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

    /**
     * Reorder the available wallets deterministically so tests that depend on
     * the delegate order (e.g. the forging order of a round) are reproducible.
     */
    public function seedWallets(int $seed): void
    {
        $this->availableWallets = collect((new Randomizer(new Mt19937($seed)))->shuffleArray(self::$wallets));
    }

    public function wallet(): array
    {
        return $this->availableWallets->shift();
    }

    public function publicKey(): string
    {
        return Str::limit(hash('sha512', Str::random(8)), 66);
    }

    public function blsPublicKey(): string
    {
        return Str::limit(hash('sha512', Str::random(8)), 96);
    }
}
