<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Block;
use App\Services\Cache\WalletCache;
use App\Services\Timestamp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class CacheLastBlockByPublicKey implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $publicKey;

    public function __construct(string $publicKey)
    {
        $this->publicKey = $publicKey;
    }

    public function handle(): void
    {
        (new WalletCache())->setLastBlock($this->publicKey, function (): array {
            $block = Block::query()
                ->without(['delegate'])
                ->where('generator_public_key', $this->publicKey)
                ->latestByHeight()
                ->limit(1)
                ->firstOrFail();

            return [
                'id'                   => $block->id,
                'height'               => $block->height->toNumber(),
                'timestamp'            => Timestamp::fromGenesis($block->timestamp)->unix(),
                'generator_public_key' => $block->generator_public_key,
            ];
        });
    }
}
