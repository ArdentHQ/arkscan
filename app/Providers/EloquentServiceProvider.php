<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\BlockRepository as BlockRepositoryContract;
use App\Contracts\RoundRepository as RoundRepositoryContract;
use App\Contracts\TransactionRepository as TransactionRepositoryContract;
use App\Contracts\WalletRepository as WalletRepositoryContract;
use App\Repositories\BlockRepository;
use App\Repositories\BlockRepositoryWithCache;
use App\Repositories\RoundRepository;
use App\Repositories\RoundRepositoryWithCache;
use App\Repositories\TransactionRepository;
use App\Repositories\TransactionRepositoryWithCache;
use App\Repositories\WalletRepository;
use App\Repositories\WalletRepositoryWithCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\ServiceProvider;
use LogicException;

final class EloquentServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerScopes();

        $this->registerRepositories();
    }

    private function registerScopes(): void
    {
        Builder::macro('withScope', function ($scope, ...$parameters): Builder {
            /** @var Builder $query */
            $query = $this;

            if (is_string($scope)) {
                $scope = new $scope(...$parameters);
            }

            if (! $scope instanceof Scope) {
                throw new LogicException('$scope must be an instance of Scope');
            }

            $scope->apply($query, $query->getModel());

            return $query;
        });
    }

    private function registerRepositories(): void
    {
        $this->app->bind(BlockRepositoryContract::class, function (): BlockRepositoryWithCache {
            return new BlockRepositoryWithCache(new BlockRepository());
        });

        $this->app->bind(RoundRepositoryContract::class, function (): RoundRepositoryWithCache {
            return new RoundRepositoryWithCache(new RoundRepository());
        });

        $this->app->bind(TransactionRepositoryContract::class, function (): TransactionRepositoryWithCache {
            return new TransactionRepositoryWithCache(new TransactionRepository());
        });

        $this->app->bind(WalletRepositoryContract::class, function (): WalletRepositoryWithCache {
            return new WalletRepositoryWithCache(new WalletRepository());
        });
    }
}
