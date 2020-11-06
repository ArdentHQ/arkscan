<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface WalletRepository
{
    public function allWithUsername(): Builder;

    public function allWithVote(): Builder;

    public function allWithPublicKey(): Builder;

    public function allWithMultiSignature(): Builder;

    public function findByAddress(string $address): Wallet;

    public function findByPublicKey(string $publicKey): Wallet;

    public function findByPublicKeys(array $publicKey): Collection;

    public function findByUsername(string $username): Wallet;

    public function findByIdentifier(string $identifier): Wallet;
}
