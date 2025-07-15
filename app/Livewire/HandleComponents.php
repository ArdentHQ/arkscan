<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Http\Livewire\Concerns\HasTabs;
use Livewire\Drawer\Utils;
use Livewire\Exceptions\PublicPropertyNotFoundException;
use Livewire\Mechanisms\HandleComponents\HandleComponents as Base;

class HandleComponents extends Base
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

        // if (in_array(HasTabs::class, class_uses_recursive($component::class), true)) {
        //     // if ($property === 'paginators') {
        //     //     $value = $component->getPage();
        //     // }

        //     $component->syncInput($path, $value);
        // } else {
        // Ensure that it's a public property, not on the base class first...
        // if (! in_array($property, array_keys(Utils::getPublicPropertiesDefinedOnSubclass($component)), true)) {
            //     throw new PublicPropertyNotFoundException($property, $component->getName());
        // }

        // If this isn't a "deep" set, set it directly, otherwise we have to
        // recursively get up and set down the value through the synths...
        if (count($segments) === 0) {
            $this->setComponentPropertyAwareOfTypes($component, $property, $value);
        } else {
            // @phpstan-ignore-next-line
            $propertyValue = $component->$property;

            $this->setComponentPropertyAwareOfTypes(
                $component,
                $property,
                $this->recursivelySetValue($property, $propertyValue, $value, $segments, 0, $context)
            );
        }
        // }

        return $finish;
    }
}
