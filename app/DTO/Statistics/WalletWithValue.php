<?php

declare(strict_types=1);

namespace App\DTO\Statistics;

use App\Models\Wallet;
use App\ViewModels\WalletViewModel;
use ARKEcosystem\Foundation\UserInterface\Support\DateFormat;
use Carbon\Carbon;

final class WalletWithValue
{
    public function __construct(public ?Wallet $wallet, public ?Carbon $timestamp)
    {
        //
    }

    public static function make(?Wallet $wallet, ?Carbon $timestamp): self
    {
        return new self($wallet, $timestamp);
    }

    public function wallet(): ?WalletViewModel
    {
        if ($this->wallet === null) {
            return null;
        }

        return new WalletViewModel($this->wallet);
    }

    public function value(): ?string
    {
        if ($this->timestamp === null) {
            return null;
        }

        return $this->timestamp->format(DateFormat::DATE);
    }
}
