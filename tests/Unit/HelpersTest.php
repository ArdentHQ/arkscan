<?php

declare(strict_types=1);

it('should truncate long strings', function () {
    expect(truncateMiddle('I am a very long string'))->toBe('I am ...tring');
    expect(truncateMiddle('I am a very long string', 10))->toBe('I am a ...string');
    expect(truncateMiddle('I am a very long string', 1))->toBe('I ...ng');
});

it('should not truncate short strings', function () {
    expect(truncateMiddle('short'))->toBe('short');
    expect(truncateMiddle('a', 10))->toBe('a');
    expect(truncateMiddle('abcd'))->toBe('abcd');
    expect(truncateMiddle('abcdefghijklmnopqrstuvwxyz', 100))->toBe('abcdefghijklmnopqrstuvwxyz');
});
