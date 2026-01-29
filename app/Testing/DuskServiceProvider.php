<?php

declare(strict_types=1);

namespace App\Testing;

use Laravel\Dusk\Browser;
use App\Testing\FakeZendesk;
use Huddle\Zendesk\Facades\Zendesk;
use Illuminate\Support\ServiceProvider;
use PHPUnit\Framework\Assert as PHPUnit;

class DuskServiceProvider extends ServiceProvider
{
    /**
     * Register Dusk's browser macros.
     */
    public function boot(): void
    {
        if (! $this->app->environment('dusk')) {
            return;
        }

        Browser::macro('assertEquals', function ($selector, $text, $ignoreCase = false) {
            $element = $this->resolver->findOrFail($selector);

            PHPUnit::assertEquals(
                $ignoreCase ? strtolower($element->getText()) : $element->getText(),
                $ignoreCase ? strtolower($text) : $text,
            );

            return $this;
        });

        Browser::macro('assertSeeInOrder', function ($text, $ignoreCase = false) {
            $element = $this->resolver->findOrFail('');
            $content = $ignoreCase ? strtolower($element->getText()) : $element->getText();
            $offset  = 0;

            foreach ($text as $textSegment) {
                $textSegment = (string) $textSegment;
                $textSegment = $ignoreCase ? strtolower($textSegment) : $textSegment;
                $position    = strpos($content, $textSegment, $offset);

                PHPUnit::assertNotFalse(
                    $position,
                    "Failed asserting that the text [{$textSegment}] was found in order.",
                );

                $offset = $position + strlen($textSegment);
            }

            return $this;
        });

        Browser::macro('waitForSeeInOrder', function ($text, $seconds = null, $ignoreCase = false) {
            $message = $this->formatTimeOutMessage('Waited %s seconds to see text segments in order', implode(', ', $text));

            return $this->waitUsing($seconds, 100, function () use ($text, $ignoreCase) {
                try {
                    $this->assertSeeInOrder($text, $ignoreCase);
                } catch (\Throwable) {
                    return false;
                }

                return true;
            }, $message);
        });

        Browser::macro('waitForQueryString', function ($queryStringKey, $expectedValue = null, $seconds = null) {
            $message = $this->formatTimeOutMessage('Waited %s seconds for querystring property', $queryStringKey);

            return $this->waitUsing($seconds, 100, function () use ($queryStringKey, $expectedValue) {
                try {
                    $this->assertQueryStringHas($queryStringKey, $expectedValue);
                } catch (\Throwable) {
                    return false;
                }

                return true;
            }, $message);
        });

        Browser::macro('waitForValue', function ($selector, $value, $seconds = null) {
            $message = $this->formatTimeOutMessage('Waited %s seconds for value of element', $selector);

            return $this->waitUsing($seconds, 100, function () use ($selector, $value) {
                try {
                    $this->assertValue($selector, $value);
                } catch (\Throwable) {
                    return false;
                }

                return true;
            }, $message);
        });

        Zendesk::swap(new FakeZendesk());
    }
}
