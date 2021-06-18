<?php

declare(strict_types=1);

use App\Services\Helpers;

it('it blends different parameters into an unique key', function () {
    $params = ['an string', true, '', null, 4];

    $str = Helpers::generateHashId(...$params);

    expect($str)->toBe('c5d9cef958');

    // Same string second time
    expect($str)->toBe(Helpers::generateHashId(...$params));
});

it('different parameters create different key', function () {
    $params1 = ['an string', true, '', null, 4];
    $params2 = ['an string', true, null, 4];
    $params3 = ['an string', true, '', null, 5];

    $str1 = Helpers::generateHashId(...$params1);
    $str2 = Helpers::generateHashId(...$params2);
    $str3 = Helpers::generateHashId(...$params3);

    expect($str1)->not->toBe($str2);
    expect($str1)->not->toBe($str3);
    expect($str2)->not->toBe($str3);
});
