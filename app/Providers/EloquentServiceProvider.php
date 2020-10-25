<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\ServiceProvider;
use LogicException;

final class EloquentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
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
}
