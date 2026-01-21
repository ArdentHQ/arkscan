<?php

declare(strict_types=1);

use App\Models\Exchange;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Stubs\WithFiltersStub;
use Tests\Stubs\WithFiltersWithoutConstantStub;

describe('with FILTERS constant', function () {
    it('should determine if there are filters in the request', function () {
        app()->instance('request', Request::create('my_url', 'GET', parameters: [
            'filter_one' => 'true',
            'filter_two' => 'false',
        ]));

        $stub = new WithFiltersStub();

        expect($stub->testHasFilter('filter_one', false))->toBeTrue();
        expect($stub->testHasFilter('filter_two', true))->toBeFalse();
        expect($stub->testHasFilter('filter_three', false))->toBeFalse();
    });

    it('should get default filters', function () {
        app()->instance('request', Request::create('my_url', 'GET', parameters: [
            'filter_one' => 'true',
            'filter_two' => 'false',
            'filter_three' => 'true',
        ]));

        $stub = new WithFiltersStub();

        expect($stub->testDefaultFilters())->toBe([
            'test-group' => [
                'filter_one' => true,
                'filter_two' => false,
            ],
        ]);

        expect($stub->testDefaultFilters('test-group'))->toBe([
            'filter_one' => true,
            'filter_two' => false,
        ]);
    });

    it('should handle default filters with invalid group', function () {
        app()->instance('request', Request::create('my_url', 'GET', parameters: [
            'filter_one' => 'true',
            'filter_two' => 'false',
            'filter_three' => 'true',
        ]));

        $stub = new WithFiltersStub();

        expect($stub->testDefaultFilters('invalid-group'))->toBe([]);
    });

    it('should get applied filters from request', function () {
        app()->instance('request', Request::create('my_url', 'GET', parameters: [
            'filter_one' => 'false',
            'filter_two' => 'true',
            'filter_three' => 'true',
        ]));

        $stub = new WithFiltersStub();

        expect($stub->testFilters('test-group'))->toBe([
            'filter_one' => false,
            'filter_two' => true,
        ]);
    });

    it('should determine if any filters are active', function () {
        app()->instance('request', Request::create('my_url', 'GET', parameters: [
            'filter_one' => 'false',
            'filter_two' => 'false',
            'filter_three' => 'true',
        ]));

        $stub = new WithFiltersStub();

        expect($stub->testHasFilters('test-group'))->toBe(false);

        app()->instance('request', Request::create('my_url', 'GET', parameters: [
            'filter_one' => 'false',
            'filter_two' => 'true',
        ]));

        $stub = new WithFiltersStub();

        expect($stub->testHasFilters('test-group'))->toBe(true);
    });
});

describe('without FILTERS constant', function () {
    it('should determine if there are filters in the request', function () {
        app()->instance('request', Request::create('my_url', 'GET', parameters: [
            'filter_one' => 'true',
            'filter_two' => 'false',
        ]));

        $stub = new WithFiltersWithoutConstantStub();

        expect($stub->testHasFilter('filter_one', false))->toBeTrue(); // True as we check the main request values here
        expect($stub->testHasFilter('filter_two', true))->toBeFalse();
        expect($stub->testHasFilter('filter_three', false))->toBeFalse();
    });

    it('should get default filters', function () {
        app()->instance('request', Request::create('my_url', 'GET', parameters: [
            'filter_one' => 'true',
            'filter_two' => 'false',
            'filter_three' => 'true',
        ]));

        $stub = new WithFiltersWithoutConstantStub();

        expect($stub->testDefaultFilters())->toBe([]);
        expect($stub->testDefaultFilters('test-group'))->toBe([]);
    });

    it('should handle default filters with invalid group', function () {
        app()->instance('request', Request::create('my_url', 'GET', parameters: [
            'filter_one' => 'true',
            'filter_two' => 'false',
            'filter_three' => 'true',
        ]));

        $stub = new WithFiltersWithoutConstantStub();

        expect($stub->testDefaultFilters('invalid-group'))->toBe([]);
    });

    it('should get applied filters from request', function () {
        app()->instance('request', Request::create('my_url', 'GET', parameters: [
            'filter_one' => 'false',
            'filter_two' => 'true',
            'filter_three' => 'true',
        ]));

        $stub = new WithFiltersWithoutConstantStub();

        expect($stub->testFilters('test-group'))->toBe([]);
    });

    it('should determine if any filters are active', function () {
        app()->instance('request', Request::create('my_url', 'GET', parameters: [
            'filter_one' => 'false',
            'filter_two' => 'false',
            'filter_three' => 'true',
        ]));

        $stub = new WithFiltersWithoutConstantStub();

        expect($stub->testHasFilters('test-group'))->toBe(false);

        app()->instance('request', Request::create('my_url', 'GET', parameters: [
            'filter_one' => 'false',
            'filter_two' => 'true',
        ]));

        $stub = new WithFiltersWithoutConstantStub();

        expect($stub->testHasFilters('test-group'))->toBe(false);
    });
});
