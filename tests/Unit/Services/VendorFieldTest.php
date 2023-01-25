<?php

use App\Services\VendorField;

it('should convert to hex', function ($content, $expected) {
    expect(VendorField::toHex($content))->toBe($expected);
})->with([
    ['0xRKeoIZ9Kh2g4HslgeHr5B9yblHbnwWYgfeFgO36n0', '3078524b656f495a394b6832673448736c6765487235423979626c48626e77575967666546674f33366e30'],
    ['1', '31'],
    [21, '3231'],
    [null, null],
]);

it('should convert resource to hex', function () {
    $resource = fopen('data:text/plain;base64,'.base64_encode('this is a test stream'), 'rb');

    expect(VendorField::toHex($resource))->toBe('74686973206973206120746573742073747265616d');
});

it('should parse vendor field value', function ($content, $expected) {
    expect(VendorField::parse($content))->toBe($expected);
})->with([
    ['3078524b656f495a394b6832673448736c6765487235423979626c48626e77575967666546674f33366e30', '0xRKeoIZ9Kh2g4HslgeHr5B9yblHbnwWYgfeFgO36n0', ],
    ['31', '1'],
    ['3231', '21'],
    [184, '184'],
    ['0xRKeoIZ9Kh2g4HslgeHr5B9yblHbnwWYgfeFgO36n0', '0xRKeoIZ9Kh2g4HslgeHr5B9yblHbnwWYgfeFgO36n0'],
    ['Random vendorfield value', 'Random vendorfield value'],
    [null, null],
    [1.4, null],
    [true, null],
]);

it('should parse resource', function ($content, $expected) {
    $resource = fopen('data:text/plain;base64,'.base64_encode($content), 'rb');

    expect(VendorField::parse($resource))->toBe($expected);
})->with([
    ['3078524b656f495a394b6832673448736c6765487235423979626c48626e77575967666546674f33366e30', '0xRKeoIZ9Kh2g4HslgeHr5B9yblHbnwWYgfeFgO36n0'],
    ['31', '1'],
    ['3231', '21'],
    ['0xRKeoIZ9Kh2g4HslgeHr5B9yblHbnwWYgfeFgO36n0', '0xRKeoIZ9Kh2g4HslgeHr5B9yblHbnwWYgfeFgO36n0'],
    ['Random vendorfield value', 'Random vendorfield value'],
]);
