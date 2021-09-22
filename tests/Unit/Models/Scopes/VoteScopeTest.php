<?php

declare(strict_types=1);

use App\Models\Scopes\VoteScope;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Scope;

beforeEach(function () {
    $this->subject = new VoteScope();
});

it('should be an instance of a scope', function () {
    expect($this->subject)->toBeInstanceOf(Scope::class);
});

it('should be added as a global scope', function () {
    expect(Transaction::hasGlobalScope(get_class($this->subject)))->toBeFalse();

    Transaction::addGlobalScope($this->subject);

    expect(Transaction::hasGlobalScope(get_class($this->subject)))->toBeTrue();
});
