<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Http\Livewire\Abstracts\TabbedComponent;
use Livewire\Drawer\Utils;
use Livewire\Exceptions\PublicPropertyNotFoundException;
use Livewire\Mechanisms\HandleComponents\HandleComponents as Base;

final class HandleComponents extends Base
{
    /**
     * Set a component property, aware of types.
     *
     * @param  mixed  $component
     * @param  mixed  $path
     * @param  mixed  $value
     * @param  mixed  $context
     * @return \Closure(mixed &$forward = null, mixed ...$extras): mixed
     */
    public function updateProperty($component, $path, $value, $context)
    {
        $segments = explode('.', $path);

        $property = array_shift($segments);

        $finish = app(\Livewire\EventBus::class)->trigger('update', $component, $path, $value);

        if (! is_a($component, TabbedComponent::class)) {
            // Ensure that it's a public property, not on the base class first...
            if (! in_array($property, array_keys(Utils::getPublicPropertiesDefinedOnSubclass($component)), true)) {
                throw new PublicPropertyNotFoundException($property, $component->getName());
            }
        }

        // If this isn't a "deep" set, set it directly, otherwise we have to
        // recursively get up and set down the value through the synths...
        if (empty($segments)) {
            $this->setComponentPropertyAwareOfTypes($component, $property, $value);
        } else {
            $propertyValue = $component->$property;

            $this->setComponentPropertyAwareOfTypes(
                $component,
                $property,
                $this->recursivelySetValue($property, $propertyValue, $value, $segments, 0, $context)
            );
        }

        return $finish;
    }
}
