<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Http\Livewire\Concerns\HasTabs;
use Livewire\Drawer\Utils;
use Livewire\Exceptions\PublicPropertyNotFoundException;
use Livewire\Mechanisms\HandleComponents\HandleComponents as Base;
use function Livewire\trigger;

class HandleComponents extends Base
{
    public function updateProperty($component, $path, $value, $context)
    {
        $segments = explode('.', $path);

        $property = array_shift($segments);

        $finish = trigger('update', $component, $path, $value);

        if (in_array(HasTabs::class, class_uses_recursive($component::class), true)) {
            if ($property === 'paginators') {
                $value = $component->getPage();
            }

            $component->syncInput($property, $value);
        } else {
            // Ensure that it's a public property, not on the base class first...
            if (! in_array($property, array_keys(Utils::getPublicPropertiesDefinedOnSubclass($component)), true)) {
                throw new PublicPropertyNotFoundException($property, $component->getName());
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
        }

        return $finish;
    }
}
