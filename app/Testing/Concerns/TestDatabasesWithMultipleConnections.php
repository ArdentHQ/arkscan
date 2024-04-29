<?php

declare(strict_types=1);

namespace App\Testing\Concerns;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Support\Facades\Schema;

trait TestDatabasesWithMultipleConnections
{
    /**
     * Indicates if the test database schema is up to date.
     *
     * @var bool
     */
    protected static $schemaIsUpToDate = false;

    protected array $additionalConnections = [
        'explorer',
    ];

    /**
     * Boot a test database.
     *
     * @return void
     */
    protected function bootTestDatabase()
    {
        ParallelTesting::setUpProcess(function () {
            $this->whenNotUsingInMemoryDatabase(function ($database, $connection) {
                if (ParallelTesting::option('recreate_databases')) {
                    if ($connection !== null) {
                        Schema::connection($connection)
                            ->dropDatabaseIfExists($this->testDatabase($database));

                        return;
                    }

                    Schema::dropDatabaseIfExists($this->testDatabase($database));
                }
            });
        });

        ParallelTesting::setUpTestCase(function ($testCase) {
            $uses = array_flip(class_uses_recursive(get_class($testCase)));

            $databaseTraits = [
                Testing\DatabaseMigrations::class,
                Testing\DatabaseTransactions::class,
                Testing\DatabaseTruncation::class,
                Testing\RefreshDatabase::class,
            ];

            if (Arr::hasAny($uses, $databaseTraits) && ! ParallelTesting::option('without_databases')) {
                $this->whenNotUsingInMemoryDatabase(function ($database, $connection) use ($uses) {
                    [$testDatabase, $created] = $this->ensureTestDatabaseExists($database);

                    $this->switchToDatabase($testDatabase, $connection);

                    if (isset($uses[Testing\DatabaseTransactions::class]) && $connection === null) {
                        $this->ensureSchemaIsUpToDate();
                    }

                    if ($created) {
                        ParallelTesting::callSetUpTestDatabaseCallbacks($testDatabase);
                    }
                });
            }
        });

        ParallelTesting::tearDownProcess(function () {
            $this->whenNotUsingInMemoryDatabase(function ($database, $connection) {
                if (ParallelTesting::option('drop_databases')) {
                    if ($connection !== null) {
                        Schema::connection($connection)
                            ->dropDatabaseIfExists($this->testDatabase($database));

                        return;
                    }

                    Schema::dropDatabaseIfExists($this->testDatabase($database));
                }
            });
        });
    }

    /**
     * Ensure a test database exists and returns its name.
     *
     * @param  string  $database
     * @param null|mixed $connection
     * @return array
     */
    protected function ensureTestDatabaseExists($database, $connection = null)
    {
        $testDatabase = $this->testDatabase($database);

        try {
            $this->usingDatabase($testDatabase, function () {
                Schema::hasTable('dummy');
            }, $connection);
        } catch (QueryException) {
            $this->usingDatabase($database, function () use ($testDatabase) {
                Schema::dropDatabaseIfExists($testDatabase);
                Schema::createDatabase($testDatabase);
            }, $connection);

            return [$testDatabase, true];
        }

        return [$testDatabase, false];
    }

    /**
     * Ensure the current database test schema is up to date.
     *
     * @return void
     */
    protected function ensureSchemaIsUpToDate()
    {
        if (! static::$schemaIsUpToDate) {
            Artisan::call('migrate');

            static::$schemaIsUpToDate = true;
        }
    }

    /**
     * Runs the given callable using the given database.
     *
     * @param  string  $database
     * @param  callable  $callable
     * @param null|mixed $connection
     * @return void
     */
    protected function usingDatabase($database, $callable, $connection = null)
    {
        if ($connection !== null) {
            $original = DB::connection($connection)->getConfig('database');
        } else {
            $original = DB::getConfig('database');
        }

        try {
            $this->switchToDatabase($database, $connection);
            $callable();
        } finally {
            $this->switchToDatabase($original, $connection);
        }
    }

    /**
     * Apply the given callback when tests are not using in memory database.
     *
     * @param  callable  $callback
     * @return void
     */
    protected function whenNotUsingInMemoryDatabase($callback)
    {
        if (ParallelTesting::option('without_databases')) {
            return;
        }

        $primaryDatabase = DB::getConfig('database');
        if ($primaryDatabase !== ':memory:') {
            $callback($primaryDatabase, null);
        }

        foreach ($this->additionalConnections as $connection) {
            $database = DB::connection($connection)->getConfig('database');
            if ($database !== ':memory:') {
                $callback($database, $connection);
            }
        }
    }

    /**
     * Switch to the given database.
     *
     * @param  string  $database
     * @param null|mixed $connection
     * @return void
     */
    protected function switchToDatabase($database, $connection = null)
    {
        // if ($connection) {
        //     DB::connection($connection)->purge();
        // } else {
        // }
        DB::purge($connection);

        if ($connection === null) {
            $connection = config('database.default');
        }

        $url = config("database.connections.{$connection}.url");

        if ($url) {
            config()->set(
                "database.connections.{$connection}.url",
                preg_replace('/^(.*)(\/[\w-]*)(\??.*)$/', "$1/{$database}$3", $url),
            );
        } else {
            config()->set(
                "database.connections.{$connection}.database",
                $database,
            );
        }
    }

    /**
     * Returns the test database name.
     *
     * @param mixed $database
     * @return string
     */
    protected function testDatabase($database)
    {
        $token = ParallelTesting::token();

        return "{$database}_test_{$token}";
    }
}
