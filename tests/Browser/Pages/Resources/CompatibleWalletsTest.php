<?php

declare(strict_types=1);

use Facebook\WebDriver\WebDriverBy;
use Laravel\Dusk\Browser;

it('should have the correct url for learn more links', function () {
    $this->browse(function (Browser $browser) {
        $browser->visitRoute('compatible-wallets')
            ->assertSee('Compatible Wallets');

        expect($browser->driver->findElements(WebDriverBy::xpath('//a[@href="'.config('arkscan.urls.public.arkvault').'"]')))->toHaveCount(1);
        expect($browser->driver->findElements(WebDriverBy::xpath('//a[@href="'.config('arkscan.urls.public.arkconnect').'"]')))->toHaveCount(1);
    });
});

it('should submit modal', function () {
    $this->browse(function (Browser $browser) {
        $browser->visitRoute('compatible-wallets')
            ->assertSee('Compatible Wallets')
            ->clickLink('Submit Wallet', 'button')
            ->assertSee('Submit a Listing')
            ->value('input[name="name"]', 'My Wallet')
            ->type('input[name="website"]', 'https://mywallet.com')
            ->value('textarea[name="message"]', 'I would like to submit my wallet for listing.')
            ->assertAttributeMissing('button[type="submit"]', 'disabled')
            ->click('button[type="submit"]')
            ->waitForText(trans('pages.compatible-wallets.submit-modal.success_toast'));
    });
});
