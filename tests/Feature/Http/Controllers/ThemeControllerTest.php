<?php

declare(strict_types=1);

it('should redirect back when updating theme', function () {
    $this
        ->post(route('theme.update'), ['theme' => 'light'])
        ->assertRedirect();
});

it('should not update settings cookie when theme is the same', function () {
    $initialSettings = ['currency' => 'USD', 'priceChart' => true, 'feeChart' => true, 'theme' => 'light'];
    $initialCookie   = json_encode($initialSettings);

    $this
        ->withCookie('settings', $initialCookie)
        ->post(route('theme.update'), ['theme' => 'light'])
        ->assertRedirect()
        ->assertCookieMissing('settings');
});

it('should update settings cookie with uppercase theme when different', function () {
    $initialSettings  = ['currency' => 'USD', 'priceChart' => true, 'feeChart' => true, 'theme' => 'light'];
    $initialCookie    = json_encode($initialSettings);
    $newTheme      = 'dark';
    $expectedSettings = ['currency' => 'USD', 'priceChart' => true, 'feeChart' => true, 'theme' => 'dark'];
    $expectedCookie   = json_encode($expectedSettings);

    $this
        ->withCookie('settings', $initialCookie)
        ->post(route('theme.update'), ['theme' => $newTheme])
        ->assertRedirect()
        ->assertCookie('settings', $expectedCookie);
});
