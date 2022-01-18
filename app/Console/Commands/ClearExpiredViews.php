<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;

final class ClearExpiredViews extends Command
{
    public const EXPIRES_MINUTES = 60;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'view:clear-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes old compiled views files';

    public function handle(Filesystem $files): void
    {
        $path = Config::get('view.compiled');

        $expiresMinutes = self::EXPIRES_MINUTES;

        collect($files->glob("{$path}/*"))
            ->filter(fn (string $view) => $files->lastModified($view) < Carbon::now()->subMinutes($expiresMinutes)->getTimestamp())
            ->each(fn (string $view)   => $files->delete($view));

        $this->info(sprintf('Compiled views that are older than %s minute(s) cleared!', $expiresMinutes));
    }
}
