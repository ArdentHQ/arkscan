<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Livewire\__stubs;

use App\Http\Livewire\Concerns\WithHooks;

/**
 * @coversNothing
 */
class WithHooksStub
{
    use WithHooks;

    public array $hooksCalled = [];

    public string $testProperty = '';

    public array $testMultidimentionalArray = [
        'default' => [
            'value' => 'default value',
        ],
    ];

    public array $testArray = [
        'default' => 'default value',
    ];

    public function callSetWithHooks(string $property, mixed $value, ?string $key = null): void
    {
        $this->setWithHooks($property, $value, $key);
    }

    public function updatingTestProperty(string $value): void
    {
        $this->hooksCalled[] = 'updatingTestProperty:'.$value;
    }

    public function updatedTestProperty(string $value): void
    {
        $this->hooksCalled[] = 'updatedTestProperty:'.$value;
    }

    public function updatingTestMultidimentionalArray(string $value, ?string $key = null): void
    {
        if ($key) {
            $this->hooksCalled[] = 'updatingTestMultidimentionalArray.'.$key.':'.$value;
        } else {
            $this->hooksCalled[] = 'updatingTestMultidimentionalArray:'.$value;
        }
    }

    public function updatedTestMultidimentionalArray(string $value, ?string $key = null): void
    {
        if ($key) {
            $this->hooksCalled[] = 'updatedTestMultidimentionalArray.'.$key.':'.$value;
        } else {
            $this->hooksCalled[] = 'updatedTestMultidimentionalArray:'.$value;
        }
    }

    public function updatingDefaultValue(string $value): void
    {
        $this->hooksCalled[] = 'updatingTestMultidimentionalArrayDefaultValue:'.$value;
    }

    public function updatedDefaultValue(string $value): void
    {
        $this->hooksCalled[] = 'updatedTestMultidimentionalArrayDefaultValue:'.$value;
    }

    public function updatingTestArray(string $value, ?string $key = null): void
    {
        if ($key) {
            $this->hooksCalled[] = 'updatingTestArray.'.$key.':'.$value;
        } else {
            $this->hooksCalled[] = 'updatingTestArray:'.$value;
        }
    }

    public function updatedTestArray(string $value, ?string $key = null): void
    {
        if ($key) {
            $this->hooksCalled[] = 'updatedTestArray.'.$key.':'.$value;
        } else {
            $this->hooksCalled[] = 'updatedTestArray:'.$value;
        }
    }

    public function updatingDefault(string $value): void
    {
        $this->hooksCalled[] = 'updatingTestArrayDefault:'.$value;
    }

    public function updatedDefault(string $value): void
    {
        $this->hooksCalled[] = 'updatedTestArrayDefault:'.$value;
    }
}
