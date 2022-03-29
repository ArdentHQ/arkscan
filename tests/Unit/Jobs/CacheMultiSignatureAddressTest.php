<?php

declare(strict_types=1);

use App\Jobs\CacheMultiSignatureAddress;
use App\Models\Wallet;
use Illuminate\Support\Facades\Cache;

it('should compute and cache the multi signature address', function () {
    $min        = 3;
    $publicKeys = [
        '02fb3def2593a00c5b84620addf28ff21bac452bd71a37d4d8e24f301683a81b56',
        '02bc9f661fcc8abca65fe9aff4614036867b7fdcc5730085ccc5cb854664d0194b',
        '03c44c6b6cc9893ae21ca606712fd0f6f03c41ce81c4f6ce5a640f4b0b82ec1ce0',
        '020300039e973baf5e46b945777cfae330d6392cdb039b1cebc5c3382d421166c3',
        '03b050073621b9b5caec9461d44d6bcf21a858c47dd88230ce723e25c1bc75c219',
    ];

    $wallet = Wallet::factory()->create([
        'attributes' => [
            'multiSignature' => [
                'min'        => $min,
                'publicKeys' => $publicKeys,
            ],
        ],
    ]);

    expect(Cache::tags('wallet')->has(md5("multi_signature/$min/".serialize($publicKeys))))->toBeFalse();

    (new CacheMultiSignatureAddress($wallet->toArray()))->handle();

    expect(Cache::tags('wallet')->has(md5("multi_signature/$min/".serialize($publicKeys))))->toBeTrue();
});
