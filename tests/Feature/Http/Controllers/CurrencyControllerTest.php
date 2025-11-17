<?php

declare(strict_types=1);

it('should redirect back when updating currency', function () {
    $this
        ->post(route('currency.update'), ['currency' => 'USD'])
        ->assertRedirect();
});

it('should not update settings cookie when currency is the same', function () {
    $initialSettings = ['currency' => 'USD'];
    $initialCookie = json_encode($initialSettings);

    $this
        ->withCookie('settings', $initialCookie)
        ->post(route('currency.update'), ['currency' => 'USD'])
        ->assertRedirect()
        ->assertCookie('settings', $initialCookie);
});

it('should update settings cookie with uppercase currency when different', function () {
    $initialSettings = ['currency' => 'USD'];
    $initialCookie = json_encode($initialSettings);
    $newCurrency = 'eur';
    $expectedSettings = ['currency' => 'EUR'];
    $expectedCookie = json_encode($expectedSettings);

    $this
        ->withCookie('settings', $initialCookie)
        ->post(route('currency.update'), ['currency' => $newCurrency])
        ->assertRedirect()
        ->assertCookie('settings', $expectedCookie);
});
