<?php

namespace App\Testing;

use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Assert as PHPUnit;

class DuskServiceProvider extends ServiceProvider
{
    /**
     * Register Dusk's browser macros.
     */
    public function boot(): void
    {
        Browser::macro('assertEquals', function ($selector, $text, $ignoreCase = false) {
            $element = $this->resolver->findOrFail($selector);

            PHPUnit::assertEquals(
                $ignoreCase ? strtolower($element->getText()) : $element->getText(),
                $ignoreCase ? strtolower($text) : $text,
            );

            return $this;
        });
    }
}
