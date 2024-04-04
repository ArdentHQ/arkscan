<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\CacheResignationId;
use App\Models\Scopes\ValidatorResignationScope;
use App\Models\Transaction;
use Illuminate\Console\Command;

final class CacheValidatorResignationIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-validator-resignation-ids';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Cache all transaction IDs for validator resignations.';

    public function handle(): void
    {
        Transaction::query()
            ->select('sender_public_key', 'id')
            ->withScope(ValidatorResignationScope::class)
            ->cursor()
            ->each(function ($transaction) {
                // @phpstan-ignore-next-line
                CacheResignationId::dispatch($transaction->sender_public_key, (string) $transaction->id)->onQueue('resignations');
            });
    }
}
