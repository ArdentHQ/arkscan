<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Contracts\ViewModel;
use App\Models\Block;
use App\Models\Round;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use InvalidArgumentException;

final class ViewModelFactory
{
    public static function make(Model $model): ViewModel
    {
        if ($model instanceof Block) {
            return new BlockViewModel($model);
        }

        if ($model instanceof Round) {
            return new RoundViewModel($model);
        }

        if ($model instanceof Transaction) {
            return new TransactionViewModel($model);
        }

        if ($model instanceof Wallet) {
            return new WalletViewModel($model);
        }

        throw new InvalidArgumentException('Invalid View Model Type.');
    }

    public static function collection(Collection $models): Collection
    {
        return $models->transform(fn ($model) => static::make($model));
    }

    public static function paginate(LengthAwarePaginator $paginator): LengthAwarePaginator
    {
        $paginator->getCollection()->transform(fn ($model) => static::make($model));

        return $paginator;
    }
}
