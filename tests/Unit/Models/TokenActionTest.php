<?php

declare(strict_types=1);

use App\Models\Token;
use App\Models\TokenAction;
use App\Models\Transaction;

beforeEach(function () {
    $this->transaction = Transaction::factory()->create();
    $this->token       = Token::factory()->create();
    $this->tokenAction = TokenAction::factory()->create([
        'transaction_hash' => $this->transaction->hash,
        'address'          => $this->token->address,
    ]);
});

it('should have a transaction', function () {
    expect($this->tokenAction->transaction->hash)->toEqual($this->transaction->hash);
});

it('should have a token', function () {
    expect($this->tokenAction->token->transaction_hash)->toEqual($this->token->transaction_hash);
});
