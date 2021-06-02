<?php

declare(strict_types=1);

namespace App\DTO;

use App\Services\Cache\WalletCache;
use App\Services\Monitor\Monitor;
use App\ViewModels\WalletViewModel;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class Slot
{
    private string $publicKey;

    private int $order;

    private WalletViewModel $wallet;

    private Carbon $forgingAt;

    private array $lastBlock;

    private string $status;

    private int $currentRoundBlocks;

    public function __construct(array $data, Collection $roundBlocks, private int $roundNumber)
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
        if ($this->isWaiting()) {
            return false;
        }

        return $this->currentRoundBlocks >= 1;
    }

    public function justMissed(): bool
    {
        if ($this->isWaiting()) {
            return false;
        }

        return $this->currentRoundBlocks < 1;
    }

    public function keepsMissing(): bool
    {
        if ($this->isWaiting()) {
            return false;
        }

        if ($this->getLastHeight() === 0) {
            return false;
        }

        // Since we're not waiting in current round, more than 1 round between current and last forged block means we're missing 2+ consecutive rounds
        return ($this->roundNumber - Monitor::roundNumberFromHeight($this->getLastHeight())) > 1;
    }

    public function missedCount(): int
    {
        return (new WalletCache())->getMissedBlocks($this->publicKey);
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

    private function isWaiting(): bool
    {
        if ($this->isNext()) {
            return true;
        }

        if ($this->isPending()) {
            return true;
        }

        return false;
    }

    private function getLastHeight(): int
    {
        return Arr::get($this->lastBlock, 'height', 0);
    }
}
