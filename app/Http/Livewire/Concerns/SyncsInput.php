<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use Livewire\Attribute;
use Livewire\Features\SupportAttributes\AttributeLevel;
use Livewire\Features\SupportLifecycleHooks\SupportLifecycleHooks;
use ReflectionClass;
use ReflectionNamedType;

trait SyncsInput
{
    public function syncInput(string $property, mixed $value): void
    {
        $hook = new SupportLifecycleHooks();

        $value = $this->castValueToProperty($property, $value);

        $hook->setComponent($this);
        $updatedHook = $hook->update($property, $property, $value);

        $propertyAttribute = $this->getAttribute($property);

        if ($propertyAttribute !== null) {
            $propertyAttribute->setValue($value);
        } else {
            data_set($this, $property, $value);
        }

        $updatedHook();
    }

    private function getAttribute(string $property): ?Attribute
    {
        return $this->getAttributes()
            ->filter(fn ($attribute) => $attribute->getLevel() === AttributeLevel::PROPERTY)
            ->keyBy('getName')
            ->get($property);
    }

    private function castValueToProperty(string $property, mixed $value): mixed
    {
        $reflection = new ReflectionClass($this);
        if (! $reflection->hasProperty($property)) {
            return $value;
        }

        $classProperty = $reflection->getProperty($property);
        $type          = $classProperty->getType();

        if ($type === null) {
            return $value;
        }

        if (! is_a($type, ReflectionNamedType::class)) {
            return $value;
        }

        if ($type->getName() === 'bool') {
            $value = in_array($value, [true, 'true', 1], true);
        }

        return $value;
    }
}
