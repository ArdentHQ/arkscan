<?php

declare(strict_types=1);

namespace App\DTO;

use App\Services\Cache\WalletCache;
use App\Services\Monitor\Monitor;
use App\ViewModels\WalletViewModel;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

final class Slot
{
    private int $currentRoundBlocks;

    public function __construct(
        private string $publicKey,
        private int $order,
        private WalletViewModel $wallet,
        private Carbon $forgingAt,
        private array $lastBlock,
        private string $status,
        private Collection $roundBlockCount,
        private int $roundNumber,
        private int $secondsUntilForge,
    ) {
        $this->currentRoundBlocks = $this->roundBlockCount
            ->get($this->publicKey, 0);
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

    public function secondsUntilForge(): int
    {
        return $this->secondsUntilForge;
    }

    public function lastBlock(): array
    {
        return $this->lastBlock;
    }

    public function roundNumber(): int
    {
        return $this->roundNumber;
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

    public function currentRoundBlocks(): int
    {
        return $this->currentRoundBlocks;
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

    public function isWaiting(): bool
    {
        if ($this->isNext()) {
            return true;
        }

        if ($this->isPending()) {
            return true;
        }

        return false;
    }

    public function clone(
        ?string $publicKey = null,
        ?int $order = null,
        ?WalletViewModel $wallet = null,
        ?Carbon $forgingAt = null,
        ?array $lastBlock = null,
        ?string $status = null,
        ?Collection $roundBlockCount = null,
        ?int $roundNumber = null,
        ?int $secondsUntilForge = null,
    ): self
    {
        return new self(
            publicKey: $publicKey ?? $this->publicKey,
            order: $order ?? $this->order,
            wallet: $wallet ?? $this->wallet,
            forgingAt: $forgingAt ?? $this->forgingAt,
            lastBlock: $lastBlock ?? $this->lastBlock,
            status: $status ?? $this->status,
            roundBlockCount: $roundBlockCount ?? $this->roundBlockCount,
            roundNumber: $roundNumber ?? $this->roundNumber,
            secondsUntilForge: $secondsUntilForge ?? $this->secondsUntilForge,
        );
    }

    private function getLastHeight(): int
    {
        return Arr::get($this->lastBlock, 'height', 0);
    }
}
