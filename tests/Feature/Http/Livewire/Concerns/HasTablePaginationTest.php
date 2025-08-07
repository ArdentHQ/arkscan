<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Tests\Feature\Http\Livewire\__stubs\HasTablePaginationStub;
use Tests\Feature\Http\Livewire\__stubs\TablePaginationComponentStub;

it('should use PER_PAGE constant if exists', function () {
    $instance = new HasTablePaginationStub();

    expect($instance->paginatorsPerPage)->toBe([]);

    expect($instance->defaultPerPage())->toBe(HasTablePaginationStub::PER_PAGE);
});

it('should use config if constant does not exist', function () {
    Config::set('arkscan.pagination.per_page', 100);

    $instance = new TablePaginationComponentStub();

    expect($instance->paginatorsPerPage)->toBe([]);

    expect($instance->defaultPerPage())->toBe(100);
});

it('should not reset page if perPage is the same', function () {
    $instance = new HasTablePaginationStub();

    $instance->setPage(2);
    $instance->setPerPage(25);

    expect($instance->getPage())->toBe(1);

    expect($instance->paginatorsPerPage)->toBe(['default' => 25]);

    $instance->gotoPage(2);
    $instance->setPerPage(25);

    expect($instance->getPage())->toBe(2);
});

it('should reset page for differently named paginator', function () {
    $instance = new HasTablePaginationStub();

    $instance->setPerPage(100, 'custom');
    $instance->setPage(2, 'custom');
    $instance->setPerPage(25, 'custom');

    expect($instance->getPage('custom'))->toBe(1);
});

it('should resolve per page from querystring', function () {
    app()->instance('request', Request::create('my_url', 'GET', parameters: [
        'per-page' => '25',
    ]));

    $instance = new HasTablePaginationStub();

    $perPage = $instance->callResolvePerPage();

    expect($perPage)->toBe(25);
});

it('should resolve default per page if invalid querystring value', function () {
    app()->instance('request', Request::create('my_url', 'GET', parameters: [
        'per-page' => 'invalid',
    ]));

    $instance = new HasTablePaginationStub();

    $perPage = $instance->callResolvePerPage();

    expect($perPage)->toBe(HasTablePaginationStub::PER_PAGE);
});
