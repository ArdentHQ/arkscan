<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

trait InteractsWithTypeData
{
    public function typeLabel(): string
    {
        return trans('general.transaction.'.$this->iconType());
    }

    public function typeComponent(): string
    {
        $view = 'transaction.details.'.Str::slug($this->iconType());

        if (View::exists("components.$view")) {
            return $view;
        }

        return 'transaction.details.fallback';
    }

    public function extraComponent(): string
    {
        return 'transaction.extra.'.trim(Str::slug($this->iconType()));
    }

    public function hasExtraData(): bool
    {
        if ($this->isMultiSignature()) {
            return true;
        }

        if ($this->isVoteCombination()) {
            return true;
        }

        if ($this->isMultiPayment()) {
            return true;
        }

        return false;
    }
}
