<?php

declare(strict_types=1);

use App\Services\ContractAbiService;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    Cache::forget('contract_abi_signatures');
});

it('returns all method signatures from ABIs and ERC-20', function () {
    $service = app(ContractAbiService::class);

    $signatures = $service->getAllSignatures();

    // Consensus ABI methods
    expect($signatures)->toHaveKey('6dd7d8ea', 'vote(address)');
    expect($signatures)->toHaveKey('3174b689', 'unvote()');
    expect($signatures)->toHaveKey('226f2645', 'registerValidator(bytes,bytes)');
    expect($signatures)->toHaveKey('b85f5da2', 'resignValidator()');
    expect($signatures)->toHaveKey('8f062626', 'updateValidator(bytes,bytes)');

    // Multipayment ABI
    expect($signatures)->toHaveKey('084ce708', 'pay(address[],uint256[])');

    // Usernames ABI
    expect($signatures)->toHaveKey('36a94134', 'registerUsername(string)');
    expect($signatures)->toHaveKey('ebed6dab', 'resignUsername()');

    // ERC-20 standard methods
    expect($signatures)->toHaveKey('a9059cbb', 'transfer(address,uint256)');
    expect($signatures)->toHaveKey('095ea7b3', 'approve(address,uint256)');
    expect($signatures)->toHaveKey('70a08231', 'balanceOf(address)');
});

it('returns a signature for a known hash', function () {
    $service = app(ContractAbiService::class);

    expect($service->getSignature('6dd7d8ea'))->toBe('vote(address)');
    expect($service->getSignature('a9059cbb'))->toBe('transfer(address,uint256)');
});

it('returns null for an unknown hash', function () {
    $service = app(ContractAbiService::class);

    expect($service->getSignature('deadbeef'))->toBeNull();
});

it('resolves known method hashes from ABIs', function () {
    $service = app(ContractAbiService::class);

    expect($service->getKnownMethodHash('vote'))->toBe('6dd7d8ea');
    expect($service->getKnownMethodHash('unvote'))->toBe('3174b689');
    expect($service->getKnownMethodHash('validator_registration'))->toBe('226f2645');
    expect($service->getKnownMethodHash('validator_resignation'))->toBe('b85f5da2');
    expect($service->getKnownMethodHash('validator_update'))->toBe('8f062626');
    expect($service->getKnownMethodHash('multipayment'))->toBe('084ce708');
    expect($service->getKnownMethodHash('username_registration'))->toBe('36a94134');
    expect($service->getKnownMethodHash('username_resignation'))->toBe('ebed6dab');
});

it('resolves static method hashes not in ABIs', function () {
    $service = app(ContractAbiService::class);

    expect($service->getKnownMethodHash('transfer'))->toBe('a9059cbb');
    expect($service->getKnownMethodHash('approve'))->toBe('095ea7b3');
    expect($service->getKnownMethodHash('contract_deployment'))->toBe('60806040');
    expect($service->getKnownMethodHash('batch_transfer'))->toBe('4885b254');
});

it('returns null for unknown method name', function () {
    $service = app(ContractAbiService::class);

    expect($service->getKnownMethodHash('unknown_method'))->toBeNull();
});

it('returns all known method hashes', function () {
    $service = app(ContractAbiService::class);

    $hashes = $service->getKnownMethodHashes();

    expect($hashes)->toHaveKey('vote');
    expect($hashes)->toHaveKey('unvote');
    expect($hashes)->toHaveKey('transfer');
    expect($hashes)->toHaveKey('approve');
    expect($hashes)->toHaveKey('multipayment');
    expect($hashes)->toHaveKey('validator_registration');
    expect($hashes)->toHaveKey('validator_resignation');
    expect($hashes)->toHaveKey('validator_update');
    expect($hashes)->toHaveKey('username_registration');
    expect($hashes)->toHaveKey('username_resignation');
    expect($hashes)->toHaveKey('contract_deployment');
    expect($hashes)->toHaveKey('batch_transfer');
});

it('caches signatures after first call', function () {
    $service = app(ContractAbiService::class);

    $service->getAllSignatures();

    expect(Cache::has('contract_abi_signatures'))->toBeTrue();
});
