<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Facades\Network;
use App\Services\Cache\WalletCache;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

final class CacheWhitelistedTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-whitelisted-tokens {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache whitelisted token addresses from a remote source.';

    public function handle(): void
    {
        $url = Network::whitelistedTokensUrl();

        if ($url === null) {
            return;
        }

        $cache = app(WalletCache::class);

        $cache->setWhitelistedTokens(
            function () use ($url): array {
                /** @var array<int, array{address: string, comment: string, createdAt: string}> $tokens */
                $tokens = Http::get($url)->json();

                return collect($tokens)
                    ->pluck('address')
                    ->map(fn (string $address) => strtolower($address))
                    ->values()
                    ->all();
            },
            $this->option('force'),
        );
    }
}
