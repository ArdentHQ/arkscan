<?php

declare(strict_types=1);

use ARKEcosystem\Foundation\UserInterface\Components\TruncateMiddle;

it('escapes ids on not found entity pages', function (string $path, bool $showsFullId) {
    $payload        = '"><img hrEF="x" sRC="data:x," oNLy=1 oNErrOR=prompt`1`>';
    $encodedPayload = rawurlencode($payload);

    $response = $this->get(sprintf($path, $encodedPayload));

    $response
        ->assertStatus(404)
        ->assertDontSee($payload, false);

    if ($showsFullId) {
        $response->assertSee(e($payload), false);

        return;
    }

    $truncateMiddle   = new TruncateMiddle();
    $truncatedPayload = $truncateMiddle->render()([
        'slot'       => $payload,
        'attributes' => ['length' => 17],
    ]);

    $response->assertSee(e($truncatedPayload), false);
})->with([
    'wallets'      => ['/wallets/%s', true],
    'transactions' => ['/transactions/%s', false],
    'blocks'       => ['/blocks/%s', false],
]);
