<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Artisan;
use Laravel\Scout\Console\ImportCommand as LaravelScoutImportCommand;

final class ImportCommand extends LaravelScoutImportCommand
{
    /**
     * Execute the console command.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function handle(Dispatcher $events)
    {
        $this->alert('IMPORTANT: Pausing scout indexing while this command is running.');
        $this->warn('If, for any reason, the process does not complete successfully');
        $this->warn('due to an error or because you killed the process, you will need');
        $this->warn('to manually resume the process To do this, use the command:');
        $this->newLine();
        $this->info(sprintf('`php artisan scout:resume-indexing` "%s"', $this->argument('model')));
        $this->newLine();

        $class = $this->argument('model');

        $model = new $class();

        Artisan::call('scout:pause-indexing', [
            'model' => $model::class,
        ]);

        parent::handle($events);

        Artisan::call('scout:resume-indexing', [
            'model' => $model::class,
        ]);
    }
}
