<?php

declare(strict_types=1);

use App\DTO\Inertia\Token as TokenDTO;
use App\Models\Token;

it('should make an instance', function () {
    $this->freezeTime();
    $this->travelTo('2025-09-11 12:00:00');

    $token = Token::factory()->create([
        'name'   => 'DARK20',
        'symbol' => 'D20',
    ]);

    $subject = TokenDTO::fromModel($token);

    expect($subject->toArray())->toEqual([
        'address'        => $token->address,
        'name'           => $token->name,
        'symbol'         => $token->symbol,
        'symbolFull'     => $token->symbol,
        'decimals'       => $token->decimals,
        'totalSupply'    => (string) $token->total_supply,
        'deploymentHash' => $token->deployment_hash,
    ]);
});

it('should truncate name and symbol', function () {
    $this->freezeTime();
    $this->travelTo('2025-09-11 12:00:00');

    $token = Token::factory()->create([
        'name'   => 'A Really Long Token Name That Exceeds The Limit',
        'symbol' => 'REALLYLONGSYMBOL',
    ]);

    $subject = TokenDTO::fromModel($token);

    expect($subject->toArray())->toEqual([
        'address'        => $token->address,
        'name'           => 'A Really Long Token',
        'symbol'         => 'REALL…',
        'symbolFull'     => 'REALLYLONGSYMBOL',
        'decimals'       => $token->decimals,
        'totalSupply'    => (string) $token->total_supply,
        'deploymentHash' => $token->deployment_hash,
    ]);
});

it('should truncate unicode text', function () {
    $this->freezeTime();
    $this->travelTo('2025-09-11 12:00:00');

    $token = Token::factory()->create([
        'name'   => 't̶͚͒̾̊̃̊̇̂̿̈́o̴̙̦͚̿̂͝k̶̡̠͖̜̥̣̦̼̺̙͓̅̈́̏̈́͗̔̔̆͘ȇ̵̛̫̥̭̪̔̆̂̋̎̏̏̉̍̽̀͝n̸̮̳̟̫̤̗͋̃̃̊̿̕̚',
        'symbol' => ' ͩͩͩͩͩͩͩͩͩ ͩͩͩ ͩͩͩ ͩͩͩͩͩͩͩͩͩͩͩͩͩͩͩͩͩͩͩͩͩͩͩͩͩͩͩͩͩͩͩͩͩ ͩͩͩ ͩͩͩ ͩͩͩ ͩͩͩá́́ͩͩͩ',
    ]);

    $subject = TokenDTO::fromModel($token);

    expect($subject->toArray())->toEqual([
        'address'        => $token->address,
        'name'           => 't̶͚͒̾̊̃̊̇̂̿̈́o̴̙̦͚̿̂͝',
        'symbol'         => 'ͩͩͩͩ…',
        'symbolFull'     => $token->symbolFull,
        'decimals'       => $token->decimals,
        'totalSupply'    => (string) $token->total_supply,
        'deploymentHash' => $token->deployment_hash,
    ]);
});
