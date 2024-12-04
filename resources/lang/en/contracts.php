<?php

declare(strict_types=1);

return [
    'a9059cbb' => 'transfer(address,uint256)',
    '23b872dd' => 'transferFrom(address,address,uint256)',
    '313ce567' => 'decimals()',
    '18160ddd' => 'totalSupply()',
    '06fdde03' => 'name()',
    '95d89b41' => 'symbol()',
    '6a911ccf' => 'deregisterValidator()',
    '084ce708' => 'pay(address[],uint256[])',
    '70a08231' => 'balanceOf(address)',
    'dd62ed3e' => 'allowance(address,address)',
    '095ea7b3' => 'approve(address,uint256)',
    '36a94134' => 'registerUsername(string)',
    'ebed6dab' => 'resignUsername()',

    // Taken from https://github.com/ArkEcosystem/mainsail/tree/develop/packages/evm-contracts/source/abis
    //
    // TODO: Possibly automate this with a job - https://app.clickup.com/t/86dv3hypu
    '0d2bd909' => 'activeValidatorsCount()',
    'b5cfa68c' => 'calculateTopValidators(uint8)',
    'f3513a37' => 'getAllValidators()',
    '40f74f47' => 'getRounds(uint256,uint256)',
    'a09686c4' => 'getRoundsCount()',
    'afeea115' => 'getTopValidators()',
    '1904bb2e' => 'getValidator(address)',
    'eb9019d4' => 'getVotes(address,uint256)',
    '1b605b86' => 'getVotesCount()',
    'd04a68c7' => 'isValidatorRegistered(address)',
    '602a9eee' => 'registerValidator(bytes)',
    'f1bd0b37' => 'registeredValidatorsCount()',
    'b85f5da2' => 'resignValidator()',
    '0777cbef' => 'resignedValidatorsCount()',
    '3174b689' => 'unvote()',
    '62525879' => 'updateValidator((address,(uint256,uint256,bool,bytes)))',
    '2bdf6d43' => 'updateVoters(address[])',
    '6dd7d8ea' => 'vote(address)',

    'argument'  => '[:index]: :value',
];
