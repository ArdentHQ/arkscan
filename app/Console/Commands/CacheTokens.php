<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Token;
use App\Services\Cache\WalletCache;
use Illuminate\Console\Command;

class CacheTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache tokens';

    public function handle(): void
    {
        $tokens = Token::all()
            ->mapWithKeys(fn (Token $token) => [strtolower($token->address) => $token]);

        (new WalletCache())->setTokens($tokens);
    }
}
