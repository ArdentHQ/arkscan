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
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\Expression;
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

        QueryBuilder::macro('joinSubLateral', function ($query, $as, $first, $operator = null, $second = null, $type = 'inner', $where = false) {
            /** @var QueryBuilder $this */

            /** @var array $subQuery */
            // @phpstan-ignore-next-line
            $subQuery = $this->createSub($query);

            $query    = $subQuery[0];
            $bindings = $subQuery[1];

            $expression = 'LATERAL ('.$query.') as '.$this->grammar->wrapTable($as);

            $this->addBinding($bindings, 'join');

            return $this->join(new Expression($expression), $first, $operator, $second, $type, $where);
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
