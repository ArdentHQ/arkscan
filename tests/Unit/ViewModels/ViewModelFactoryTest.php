<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\ForgingStats;
use App\Models\Round;
use App\Models\Transaction;
use App\Models\Wallet;
use App\ViewModels\BlockViewModel;
use App\ViewModels\ForgingStatsViewModel;
use App\ViewModels\RoundViewModel;
use App\ViewModels\TransactionViewModel;
use App\ViewModels\ViewModelFactory;
use App\ViewModels\WalletViewModel;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Tests\InvalidModel;

it('should make a view model', function ($modelClass, $viewModel) {
    expect(ViewModelFactory::make($modelClass::factory()->create()))->toBeInstanceOf($viewModel);
})->with([
    [Block::class, BlockViewModel::class],
    [Round::class, RoundViewModel::class],
    [Transaction::class, TransactionViewModel::class],
    [Wallet::class, WalletViewModel::class],
    [ForgingStats::class, ForgingStatsViewModel::class],
]);

it('should make a view model collection', function ($modelClass, $viewModel) {
    $models = new EloquentCollection();
    for ($i = 0; $i < 10; $i++) {
        try {
            $models->add($modelClass::factory()->create());
        } catch (Exception $e) {
            if (str_contains($e->getMessage(), 'duplicate key value violates')) {
                $i--;
            }
        }
    }

    expect(ViewModelFactory::collection($models))->toBeInstanceOf(Collection::class);

    foreach ($models as $model) {
        expect($model)->toBeInstanceOf($viewModel);
    }
})->with([
    [Block::class, BlockViewModel::class],
    [Round::class, RoundViewModel::class],
    [Transaction::class, TransactionViewModel::class],
    [Wallet::class, WalletViewModel::class],
    [ForgingStats::class, ForgingStatsViewModel::class],
]);

it('cannot make an invalid view model', function () {
    $this->expectException(InvalidArgumentException::class);

    ViewModelFactory::make(new InvalidModel());
})->throws(InvalidArgumentException::class);
