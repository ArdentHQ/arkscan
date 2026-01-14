<?php

declare(strict_types=1);

use App\Http\Controllers\Inertia\SupportController;
use Huddle\Zendesk\Facades\Zendesk;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Laravel\Dusk\Browser;

beforeEach(function () {
    $this->withoutExceptionHandling();
});

it('should show the page', function ($resolution) {
    $this->browse(function (Browser $browser) use ($resolution) {
        $browser->resize($resolution['width'], $resolution['height']);

        $browser->visitRoute('contact')
            ->waitForText(trans('pages.support.form.title'))
            ->assertPresent('a[href="https://x.com/Ardent_HQ"]')
            ->assertPresent('a[href="https://github.com/ArdentHQ"]')
            ->assertPresent('input[name="name"]')
            ->assertPresent('input[name="email"]')
            ->assertPresent('select[name="subject"]')
            ->assertPresent('textarea[name="message"]')
            ->assertPresent('button[type="submit"]')
            ->assertPresent('form#contact-form');
    });
})->with('resolutions');

it('should error on submit', function ($resolution) {
    $this->browse(function (Browser $browser) use ($resolution) {
        $browser->resize($resolution['width'], $resolution['height']);

        $browser->visitRoute('contact')
            ->waitForText(trans('pages.support.form.title'))
            ->click('button[type="submit"]')
            ->waitFor('.input-text--error')
            ->mouseOver('[data-testid="contact:form:name"] .tooltip-content')
            ->assertSee('The name field is required.');
    });
})
->with('resolutions');

it('should submit', function ($resolution) {
    $this->browse(function (Browser $browser) use ($resolution) {
        $browser->resize($resolution['width'], $resolution['height']);

        $browser->visitRoute('contact')
            ->waitForText(trans('pages.support.form.title'))
            ->value('input[name="name"]', 'Dusk Tester')
            ->value('input[name="email"]', 'test@test.com')
            ->value('select[name="subject"]', 'other')
            ->value('textarea[name="message"]', 'This is a test message.')
            ->click('button[type="submit"]')
            ->waitForValue('input[name="name"]', '')
            ->assertValue('input[name="email"]', '')
            ->assertValue('select[name="subject"]', 'general')
            ->assertValue('textarea[name="message"]', '');
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
