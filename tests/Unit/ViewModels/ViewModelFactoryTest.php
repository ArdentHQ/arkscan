<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Models\Wallet;
use App\ViewModels\TransactionViewModel;
use App\ViewModels\ViewModelFactory;
use App\ViewModels\WalletViewModel;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Tests\InvalidModel;

it('should make a view model', function ($modelClass, $viewModel) {
    expect(ViewModelFactory::make($modelClass::factory()->create()))->toBeInstanceOf($viewModel);
})->with([
    [Transaction::class, TransactionViewModel::class],
    [Wallet::class, WalletViewModel::class],
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
    [Transaction::class, TransactionViewModel::class],
    [Wallet::class, WalletViewModel::class],
]);

it('cannot make an invalid view model', function () {
    $this->expectException(InvalidArgumentException::class);

    ViewModelFactory::make(new InvalidModel());
})->throws(InvalidArgumentException::class);

it('should paginate a view model collection', function () {
    $models = Transaction::factory()->count(10)->create();

    $paginator = new LengthAwarePaginator($models, 10, 5);

    $paginatedViewModels = ViewModelFactory::paginate($paginator);

    expect($paginatedViewModels)->toBeInstanceOf(LengthAwarePaginator::class);
    expect($paginatedViewModels->total())->toBe(10);

    foreach ($paginator->getCollection() as $model) {
        expect($model)->toBeInstanceOf(TransactionViewModel::class);
    }
});
