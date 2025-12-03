<?php

declare(strict_types=1);

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

        Browser::macro('waitForQueryString', function ($queryStringKey, $expectedValue = null, $seconds = null) {
            $message = $this->formatTimeOutMessage('Waited %s seconds for querystring property', $queryStringKey);

            return $this->waitUsing($seconds, 100, function () use ($queryStringKey, $expectedValue) {
                try {
                    $this->assertQueryStringHas($queryStringKey, $expectedValue);
                } catch (\Throwable $e) {
                    return false;
                }

                return true;
            }, $message);
        });
    }
}
