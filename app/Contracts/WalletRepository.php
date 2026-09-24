<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface WalletRepository
{
    /** @return Builder<Wallet> */
    public function allWithUsername(): Builder;

    /** @return Builder<Wallet> */
    public function allWithVote(): Builder;

    /** @return Builder<Wallet> */
    public function allWithPublicKey(): Builder;

    /** @return Builder<Wallet> */
    public function allWithMultiSignature(): Builder;

    public function findByAddress(string $address): Wallet;

    public function findByPublicKey(string $publicKey): Wallet;

    /** @return Collection<int, Wallet> */
    public function findByPublicKeys(array $publicKey): Collection;

    public function findByUsername(string $username, bool $caseSensitive = true): Wallet;

    public function findByIdentifier(string $identifier): Wallet;
}
