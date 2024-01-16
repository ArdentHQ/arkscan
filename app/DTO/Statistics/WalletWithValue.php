<?php

declare(strict_types=1);

namespace App\DTO\Statistics;

use App\Models\Wallet;
use App\ViewModels\WalletViewModel;
use ARKEcosystem\Foundation\UserInterface\Support\DateFormat;
use Carbon\Carbon;

final class WalletWithValue
{
    public static function make(Wallet $wallet, Carbon $timestamp): self
    {
        return new self($wallet, $timestamp);
    }

    public function __construct(public Wallet $wallet, public Carbon $timestamp)
    {
        //
    }

    public function wallet(): WalletViewModel
    {
        return new WalletViewModel($this->wallet);
    }

    public function value(): string
    {
        return $this->timestamp->format(DateFormat::DATE);
    }
}
