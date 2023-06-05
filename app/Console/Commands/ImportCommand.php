<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Artisan;
use Laravel\Scout\Console\ImportCommand as LaravelScoutImportCommand;

final class ImportCommand extends LaravelScoutImportCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scout:import
            {model : Class name of model to bulk import}
            {--c|chunk= : The number of records to import at a time (Defaults to configuration value: `scout.chunk.searchable`)}
            {--no-pause : Do not pause indexing while this command is running}';

    /**
     * Execute the console command.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function handle(Dispatcher $events)
    {
        $this->alert('IMPORTANT: Pausing scout indexing while this command is running.');
        $this->warn('If, for any reason, the process does not complete successfully due');
        $this->warn('to an error or because you killed the process, you will need to');
        $this->warn('manually resume the scout indexing process. In production environments,');
        $this->warn('you shouldn\'t manually resume this process, but start the import again.');
        $this->warn('To resume indexing use the command:.');
        $this->newLine();
        $this->warn(sprintf('`php artisan scout:resume-indexing "%s"`', $this->argument('model')));
        $this->newLine();

        $class = $this->argument('model');
        $pause = ! $this->option('no-pause');

        $model = new $class();

        if ($pause) {
            Artisan::call('scout:pause-indexing', [
                'model' => $model::class,
            ]);
        }

        parent::handle($events);

        if ($pause) {
            Artisan::call('scout:resume-indexing', [
                'model' => $model::class,
            ]);
        }
    }
}
