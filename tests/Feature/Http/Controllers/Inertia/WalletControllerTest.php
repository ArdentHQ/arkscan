<?php

declare(strict_types=1);

use App\Models\Wallet;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->withoutExceptionHandling();
});

function performWalletRequest($context, $pageCallback = null, ?Wallet $wallet = null): mixed
{
    if ($wallet === null) {
        $wallet = Wallet::factory()->create();
    }

    return $context->get(route('wallet-inertia', $wallet))
        ->assertOk()
        ->assertInertia(function (Assert $page) use ($pageCallback, $wallet) {
            $page->where('wallet.address', $wallet->address)
                ->component('Wallet');

            if (is_callable($pageCallback)) {
                $pageCallback($page);
            }
        });
}

it('should render the page without any errors', function () {
    performWalletRequest($this);
});
