<?php

declare(strict_types=1);

use Facebook\WebDriver\WebDriverBy;
use Laravel\Dusk\Browser;

it('should have the correct url for learn more links', function ($resolution) {
    $this->browse(function (Browser $browser) use ($resolution) {
        $browser->resize($resolution['width'], $resolution['height']);

        $browser->visitRoute('compatible-wallets')
            ->assertSee('Compatible Wallets');

        expect($browser->driver->findElements(WebDriverBy::xpath('//a[@href="'.config('arkscan.urls.public.arkvault').'"]')))->toHaveCount(1);
        expect($browser->driver->findElements(WebDriverBy::xpath('//a[@href="'.config('arkscan.urls.public.arkconnect').'"]')))->toHaveCount(1);
    });
})->with('resolutions');

it('should link the submit button to a mailto', function ($resolution) {
    $this->browse(function (Browser $browser) use ($resolution) {
        $browser->resize($resolution['width'], $resolution['height']);

        $browser->visitRoute('compatible-wallets')
            ->assertSee('Compatible Wallets')
            ->assertSeeLink('Submit Wallet');

        $href = 'mailto:'.config('mail.contact_email').'?subject='.rawurlencode(trans('pages.compatible-wallets.submit_email_subject'));

        expect($browser->driver->findElements(WebDriverBy::xpath('//a[@href="'.$href.'"]')))->toHaveCount(1);
    });
})->with('resolutions');

dataset('resolutions', [
    'desktop' => [['width' => 1280, 'height' => 1024]],
    'lg'      => [['width' => 1024, 'height' => 768]],
    'md-lg'   => [['width' => 960, 'height' => 667]],
    'md'      => [['width' => 768, 'height' => 1024]],
    'sm'      => [['width' => 640, 'height' => 960]],
    'xs'      => [['width' => 370, 'height' => 844]],
]);
