<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\CacheResignationId;
use App\Models\Scopes\DelegateResignationScope;
use App\Models\Transaction;
use Illuminate\Console\Command;

final class CacheResignationIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:resignation-ids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        Transaction::query()
            ->withScope(DelegateResignationScope::class)
            ->cursor()
            ->each(fn ($transaction) => CacheResignationId::dispatch($transaction));
    }
}
