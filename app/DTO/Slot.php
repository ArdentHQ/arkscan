<?php

declare(strict_types=1);

namespace App\DTO;

use App\ViewModels\WalletViewModel;
use Carbon\Carbon;
use Illuminate\Support\Str;

final class Slot
{
    private int $order;

    private WalletViewModel $wallet;

    private Carbon $forgingAt;

    private array $lastBlock;

    private bool $isSuccess;

    private bool $isWarning;

    private bool $isDanger;

    private int $missedCount;

    private string $status;

    private int $time;

    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            /* @phpstan-ignore-next-line */
            $key = Str::camel($key);

            /* @phpstan-ignore-next-line */
            $this->$key = $value;
        }
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

    public function isSuccess(): bool
    {
        return $this->isSuccess;
    }

    public function isWarning(): bool
    {
        return $this->isWarning;
    }

    public function isDanger(): bool
    {
        return $this->isDanger;
    }

    public function missedCount(): int
    {
        return $this->missedCount;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function time(): int
    {
        return $this->time;
    }
}
