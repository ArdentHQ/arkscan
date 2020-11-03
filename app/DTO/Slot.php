<?php

declare(strict_types=1);

namespace App\DTO;

use App\Facades\Network;
use App\Services\Cache\NetworkCache;
use App\ViewModels\WalletViewModel;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class Slot
{
    private int $order;

    private WalletViewModel $wallet;

    private Carbon $forgingAt;

    private array $lastBlock;

    private string $status;

    private int $currentRoundBlocks;

    public function __construct(array $data, Collection $roundBlocks)
    {
        foreach ($data as $key => $value) {
            /* @phpstan-ignore-next-line */
            $key = Str::camel($key);

            /* @phpstan-ignore-next-line */
            $this->$key = $value;
        }

        $this->currentRoundBlocks = $roundBlocks
            ->where('generator_public_key', $data['publicKey'])
            ->count();
    }

    public function publicKey(): string
    {
        return $this->publicKey;
    }

    public function order(): int
    {
        return $this->order;
    }

    public function wallet(): WalletViewModel
    {
        return $this->wallet;
    }

    public function forgingAt(): Carbon
    {
        return $this->forgingAt;
    }

    public function lastBlock(): array
    {
        return $this->lastBlock;
    }

    public function hasForged(): bool
    {
        if ($this->isPending()) {
            return false;
        }

        return $this->currentRoundBlocks >= 1;
    }

    public function justMissed(): bool
    {
        if ($this->isNext()) {
            return false;
        }

        if ($this->isPending()) {
            return false;
        }

        return $this->missedCount() > (Network::delegateCount() * 2);
    }

    public function keepsMissing(): bool
    {
        if ($this->isNext()) {
            return false;
        }

        if ($this->isPending()) {
            return false;
        }

        return $this->missedCount() > (Network::delegateCount() * 3);
    }

    public function missedCount(): int
    {
        return (int) abs((new NetworkCache())->getHeight() - $this->lastBlock['height']);
    }

    public function isDone(): bool
    {
        return $this->status === 'done';
    }

    public function isNext(): bool
    {
        return $this->status === 'next';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function status(): string
    {
        return $this->status;
    }
}
