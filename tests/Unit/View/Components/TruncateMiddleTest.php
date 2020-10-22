<?php

declare(strict_types=1);

use App\View\Components\TruncateMiddle;

it('should truncate long strings', function () {
    expect((new TruncateMiddle('I am a very long string'))->render())->toBe('I am ...tring');
    expect((new TruncateMiddle('I am a very long string', 10))->render())->toBe('I am a ...string');
    expect((new TruncateMiddle('I am a very long string', 1))->render())->toBe('I ...ng');
});

it('should not truncate short strings', function () {
    expect((new TruncateMiddle('short'))->render())->toBe('short');
    expect((new TruncateMiddle('a', 10))->render())->toBe('a');
    expect((new TruncateMiddle('abcd'))->render())->toBe('abcd');
    expect((new TruncateMiddle('abcdefghijklmnopqrstuvwxyz', 100))->render())->toBe('abcdefghijklmnopqrstuvwxyz');
});
