<?php

declare(strict_types=1);

use App\Models\Token;
use App\Models\TokenTransfer;
use App\Models\Transaction;

beforeEach(function () {
    $this->transaction   = Transaction::factory()->create();
    $this->token         = Token::factory()->create();
    $this->tokenTransfer = TokenTransfer::factory()->create([
        'transaction_hash' => $this->transaction->hash,
        'address'          => $this->token->address,
    ]);
});

it('should have a transaction', function () {
    expect($this->tokenTransfer->transaction->hash)->toEqual($this->transaction->hash);
});

it('should have a token', function () {
    expect($this->tokenTransfer->token->transaction_hash)->toEqual($this->token->transaction_hash);
});
