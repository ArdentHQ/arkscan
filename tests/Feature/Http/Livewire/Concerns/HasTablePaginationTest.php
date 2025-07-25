<?php

declare(strict_types=1);

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
