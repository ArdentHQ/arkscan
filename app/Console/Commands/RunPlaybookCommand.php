<?php

declare(strict_types=1);

namespace  App\Console\Commands;

use App\Console\Playbooks\Playbook;
use App\Console\Playbooks\PlaybookDefinition;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Question\Question;

final class RunPlaybookCommand extends Command
{
    protected $signature = 'playbook:run {playbook?}';

    protected $description = 'Setup the database against a predefined playbook';

    protected array $ranDefinitions = [];

    public function handle(): void
    {
        if (app()->environment() !== 'local') {
            $this->error('This command can only be run in the local environment!');
        }

        $playbookName = $this->argument('playbook');

        if (is_null($playbookName)) {
            $availablePlaybooks = $this->getAvailablePlaybooks();

            $this->comment('Choose a playbook: '.PHP_EOL);

            foreach ($availablePlaybooks as $availablePlaybook) {
                $this->comment("- {$availablePlaybook}");
            }

            $this->comment('');

            $playbookName = $this->askPlaybookName($availablePlaybooks);
        }

        $playbookDefinition = $this->resolvePlaybookDefinition($playbookName);

        $this->migrate();

        $this->runPlaybook($playbookDefinition);
    }

    private function migrate(): void
    {
        $this->info('Clearing the database');

        $this->call('migrate:fresh', ['--force']);
    }

    private function runPlaybook(PlaybookDefinition $definition): void
    {
        foreach ($definition->playbook->before() as $before) {
            $this->runPlaybook(
                $this->resolvePlaybookDefinition($before)
            );
        }

        for ($i = 1; $i <= $definition->times; $i++) {
            if ($definition->once && $this->definitionHasRun($definition)) {
                break;
            }

            $this->infoRunning($definition->playbook, $i);

            $definition->playbook->run($this->input, $this->output);

            $definition->playbook->hasRun();

            $this->ranDefinitions[$definition->id] = ($this->ranDefinitions[$definition->id] ?? 0) + 1;
        }

        foreach ($definition->playbook->after() as $after) {
            $this->runPlaybook(
                $this->resolvePlaybookDefinition($after)
            );
        }
    }

    private function askPlaybookName(array $availablePlaybooks): string
    {
        $helper = $this->getHelper('question');

        $question = new Question('');

        $question->setAutocompleterValues($availablePlaybooks);

        $playbookName = (string) $helper->ask($this->input, $this->output, $question);

        if (is_null($playbookName)) {
            $this->error('Please choose a playbook');

            return $this->askPlaybookName($availablePlaybooks);
        }

        return $playbookName;
    }

    private function getAvailablePlaybooks(): array
    {
        $files = scandir(__DIR__.'/../Playbooks');

        unset($files[0], $files[1]);

        return array_map(fn (string $file) => str_replace('.php', '', $file), $files);
    }

    private function resolvePlaybookDefinition($class): PlaybookDefinition
    {
        if ($class instanceof PlaybookDefinition) {
            return $class;
        }

        if ($class instanceof Playbook) {
            return new PlaybookDefinition(get_class($class));
        }

        $className = $class;

        if (! Str::startsWith($class, ['\\App\\Console\\Playbooks', 'App\\Console\\Playbooks'])) {
            $className = "\\App\\Console\\Playbooks\\{$class}";
        }

        return new PlaybookDefinition($className);
    }

    private function infoRunning(Playbook $playbook, int $i): void
    {
        $playbookName = get_class($playbook);

        $this->info("Running playbook `{$playbookName}` (#{$i})");
    }

    private function definitionHasRun(PlaybookDefinition $definition): bool
    {
        return array_key_exists($definition->id, $this->ranDefinitions);
    }
}
