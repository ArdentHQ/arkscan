<?php

use Tests\Feature\Http\Livewire\__stubs\HasTableSortingStub;

it('should have sorting querystring data', function () {
    $instance = new HasTableSortingStub();

    expect($instance->queryStringHasTableSorting())->toBe([
        'sortKeys.default'       => ['as' => 'sort', 'except' => 'age'],
        'sortDirections.default' => ['as' => 'sort-direction', 'except' => 'desc'],
    ]);
});
