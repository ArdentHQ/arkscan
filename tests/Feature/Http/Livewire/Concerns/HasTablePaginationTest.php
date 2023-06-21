<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;
use Tests\Feature\Http\Livewire\__stubs\TablePaginationComponentStub;

class HasTablePaginationTest extends TablePaginationComponentStub
{
    public const PER_PAGE = 50;
}

it('should use PER_PAGE constant if exists', function () {
    $instance = new TablePaginationComponentStubWithConstant();

    expect(isset($instance->perPage))->toBeFalse();

    $instance->bootHasTablePagination();

    expect($instance->perPage)->toBe(TablePaginationComponentStubWithConstant::PER_PAGE);
});

it('should use config if constant does not exist', function () {
    Config::set('arkscan.pagination.per_page', 100);

    $instance = new TablePaginationComponentStub();

    expect(isset($instance->perPage))->toBeFalse();

    $instance->bootHasTablePagination();

    expect($instance->perPage)->toBe(100);
});
