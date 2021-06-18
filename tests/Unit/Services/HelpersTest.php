<?php

declare(strict_types=1);

use App\Services\Helpers;

it('it blends different parameters into an unique string', function () {
    $params = ['somestring', true, '', null, 4];

    $str = Helpers::generateId(...$params);

    expect($str)->toBe('somestring*1***4');

    // Same string second time
    expect($str)->toBe(Helpers::generateId(...$params));
});

it('different parameters create different string', function () {
    $params1 = ['some-string', true, '', null, 4];
    $params2 = ['some-string', true, null, 4];
    $params3 = ['some-string', true, '', null, 5];

    $str1 = Helpers::generateId(...$params1);
    $str2 = Helpers::generateId(...$params2);
    $str3 = Helpers::generateId(...$params3);

    expect($str1)->not->toBe($str2);
    expect($str1)->not->toBe($str3);
    expect($str2)->not->toBe($str3);
});
