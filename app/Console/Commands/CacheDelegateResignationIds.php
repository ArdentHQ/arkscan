<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\CacheResignationId;
use App\Models\Scopes\DelegateResignationScope;
use App\Models\Transaction;
use Illuminate\Console\Command;

final class CacheDelegateResignationIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-delegate-resignation-ids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache all transaction IDs for delegate resignations.';

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
            ->each(fn ($transaction) => CacheResignationId::dispatch($transaction)->onQueue('resignations'));
    }
}
