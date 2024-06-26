<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use Illuminate\Support\Collection;
use Livewire\Features\SupportAttributes\Attribute;
use Livewire\Features\SupportAttributes\AttributeLevel;
use Livewire\Features\SupportLifecycleHooks\SupportLifecycleHooks;

trait SyncsInput
{
    public function syncInput(string $property, mixed $value): void
    {
        $hook = new SupportLifecycleHooks();

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
        return $this->getAttributesByName()
            ->get($property);
    }

    private function getAttributesByName(): Collection
    {
        return $this->getAttributes()
            ->filter(fn ($attribute) => $attribute->getLevel() === AttributeLevel::PROPERTY)
            ->mapWithKeys(fn ($attribute) => [$attribute->getName() => $attribute]);
    }
}
