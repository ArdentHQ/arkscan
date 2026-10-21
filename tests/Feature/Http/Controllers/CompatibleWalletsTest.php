<?php

declare(strict_types=1);

it('should render the page without any errors', function () {
    $this->get(route('compatible-wallets'))
        ->assertOk();
});

it('should link the submit button to a mailto', function () {
    $href = 'mailto:'.config('mail.contact_email').'?subject='.rawurlencode(trans('pages.compatible-wallets.submit_email_subject'));

    $this->get(route('compatible-wallets'))
        ->assertSee('href="'.$href.'"', false)
        ->assertSee(trans('actions.submit_wallet'));
});
