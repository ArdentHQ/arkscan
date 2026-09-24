<?php

declare(strict_types=1);

namespace App\Testing\Concerns;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Testing\Concerns\TestDatabases;

/* Heavily based on \Illuminate\Testing\Concerns\TestDatabases. */
trait TestDatabasesWithMultipleConnections
{
    use TestDatabases;

    protected array $additionalConnections = [
        'explorer',
    ];

    /**
     * The root database name prior to concatenating the token, keyed by
     * connection name (see testDatabaseForConnection()).
     *
     * @var array<string, string>
     */
    protected static array $originalDatabaseNames = [];

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
                            ->dropDatabaseIfExists($this->testDatabaseForConnection($database, $connection));

                        return;
                    }

                    Schema::dropDatabaseIfExists($this->testDatabaseForConnection($database, $connection));
                }
            });
        });

        ParallelTesting::setUpTestCase(function ($testCase) {
            $uses = array_flip(class_uses_recursive(get_class($testCase)));

            $databaseTraits = [
                DatabaseMigrations::class,
                DatabaseTransactions::class,
                DatabaseTruncation::class,
                RefreshDatabase::class,
            ];

            if (Arr::hasAny($uses, $databaseTraits) && ! ParallelTesting::option('without_databases')) {
                $this->whenNotUsingInMemoryDatabase(function ($database, $connection) use ($uses) {
                    [$testDatabase, $created] = $this->ensureTestDatabaseExists($database, $connection);

                    $this->switchToDatabase($testDatabase, $connection);

                    if (isset($uses[DatabaseTransactions::class]) && $connection === null) {
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
                            ->dropDatabaseIfExists($this->testDatabaseForConnection($database, $connection));

                        return;
                    }

                    Schema::dropDatabaseIfExists($this->testDatabaseForConnection($database, $connection));
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
        $testDatabase = $this->testDatabaseForConnection($database, $connection);

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
     * Returns the test database name for the given connection.
     *
     * Laravel's own `TestDatabases::testDatabase()` caches the *first*
     * database name it ever sees in a single static property and reuses it
     * for every subsequent call, regardless of connection - fine when a
     * test suite only ever has one base database name, but it silently
     * collapses this app's separate 'pgsql' and 'explorer' test databases
     * onto the same physical database (the second `migrate:fresh` call then
     * wipes out the tables the first one just created). Cache per
     * connection instead.
     *
     * @param  string  $database
     * @param null|mixed $connection
     * @return string
     */
    protected function testDatabaseForConnection($database, $connection = null)
    {
        $key = $connection ?? 'default';

        if (! isset(self::$originalDatabaseNames[$key])) {
            self::$originalDatabaseNames[$key] = $database;
        } else {
            $database = self::$originalDatabaseNames[$key];
        }

        $token = ParallelTesting::token();

        return "{$database}_test_{$token}";
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
}
