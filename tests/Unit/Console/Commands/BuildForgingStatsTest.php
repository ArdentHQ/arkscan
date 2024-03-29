<?php

declare(strict_types=1);

use App\Facades\Services\Monitor\MissedBlocksCalculator;
use App\Models\Block;
use App\Models\ForgingStats;
use App\Models\Round;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    ForgingStats::truncate();

    $delegatePublicKeysBalanceDesc = [
        '027716e659220085e41389efc7cf6a05f7f7c659cf3db9126caabce6cda9156582',
        '03d3c6889608074b44155ad2e6577c3368e27e6e129c457418eb3e5ed029544e8d',
        '032cfbb18f4e49952c6d6475e8adc6d0cba00b81ef6606cc4927b78c6c50558beb',
        '0332131d247b4a2f5c81f05ecccee58d76c57e40ec82247b11380a07c6f7971ac2',
        '03eda1b9127d9a12a7c6903ca896534937ec492afa12ffa57a9aa6f3c77b618fdb',
        '0352e9ea81b7fb78b80ab6598e66d23764249c77b9492e3c1b0705d9d0722b729f',
        '02062f6f6d2aabafd745e6b01f4aa788a012c4ce7131312026bdb6eb4e74a464d2',
        '0236d5232cdbd1e7ab87fad10ebe689c4557bc9d0c408b6773be964c837231d5f0',
        '023ee98f453661a1cb765fd60df95b4efb1e110660ffb88ae31c2368a70f1f7359',
        '035c14e8c5f0ee049268c3e75f02f05b4246e746dc42f99271ff164b7be20cf5b8',
        '0304d0c477d634cc85d89c1a4afee8f51168d1747fe8fd79cabc26565e49eb8a7a',
        '039b5a3a71335bfa6c72b82498f814123e0678f7cd3d8e7221ec7124918736e01c',
        '02e345079aca0567db96ec0ba3acf859b7cfd15134a855671f9c0fe8b1173767bd',
        '02789894f309f08a4e7833452552aa39e168005d893cafc8ef995edbfdba396d2c',
        '024d5eacc5e05e1b05c476b367b7d072857826d9b271e07d3a3327224db8892a21',
        '02747353898e59c4f784542f357d5dd938a2872adb53abb94924091fddfdd83dc3',
        '03b12f99375c3b0e4f5f5c7ea74e723f0b84a6f169b47d9105ed2a179f30c82df2',
        '023e3b421c730f85d2db546ee58f2b8d81dc141c3b12f8b8efadba8ddf085a4db6',
        '02d0244d939fad9004cc104f71b46b428d903e4f2988a65f39fdaa1b7482894c9e',
        '03fc2f83a26d59a0406f0bf7ba0f5f2271f723fe502fd2390de26c134d15b2cffd',
        '0242555e90957de10e3912ce8831bcc985a40a645447dbfe8a0913ee6d89914707',
        '037997a6553ea8073eb199e9f5ff23b8f0892e79433ef35e13966e0a12849d02e3',
        '02257c58004e5ae23716d1c44beea0cca7f5b522a692df367bae9015a4f15c1670',
        '02faa7fe3167685f42daa84c6a8e989af88878843cb9a8fd72e045a46a83a1afca',
        '03ccf15ff3a07e1a4b04692f7f2db3a06948708dacfff47661c259f2fa689e1941',
        '022ffb5fa4eb5b2e71c985b1d796642528802f04a6ddf9a449ba1aab292a9744aa',
        '02cd9f56a176c843724eb58d3ef89dc88915a814bdcf284b02933e0dd203630a83',
        '037850667ea2c8473adf7c87ee4496af1b7821f4e10761e78c3b391d6fcfbde9bb',
        '02294cdcedcf6a016fff62d9a8f8a156383b57ebc150dcc9490ddf6e5ea824614f',
        '03b906102928cf97c6ddeb59cefb0e1e02105a22ab1acc3b4906214a16d494db0a',
        '0250b742256f9321bd7d46f3ed9769b215a7c2fb02be951acf43bc51eb57ceadf6',
        '02c3d1ae1b8fe831218f78cf09d864e60818ebdba4aacc74ecc2bcf2734aadf5ea',
        '02951227bb3bc5309aeb96460dbdf945746012810bb4020f35c20feae4eea7e5d4',
        '022eedf9f1cdae0cfaae635fe415b6a8f1912bc89bc3880ec41135d62cbbebd3d3',
        '0306950dae7158103814e3828b1ab97a87dbb3680db1b4c6998b8208865b2f9db7',
        '02aabcdb8511f55b6a28593979b726ef55b1f5bbf16a83205c2e2bfc9d8c2909e3',
        '03a8ff0a3cbdcb3bfbdb84dbf83226f338ba1452047ac5b8228a1513f7f1de80de',
        '03f08761f99892996c6771761955ec41ee6cdffadd43171228f5f28f8c76423b3d',
        '02ff842d25fc8eec9e1382e6468188b3fd130ab6246240fc97958ce83d6d147eaf',
        '031a6d8dab63668e901661c592dfe4bcc75793959d6ee6300408482840487d1faf',
        '0259d9ca7922c277b0e7407a88703bbb98f5da43a335b0eefa6c4642f072acfe79',
        '0296893488d335ff818391da7c450cfeb7821a4eb535b15b95808ea733915fbfb1',
        '03ce92e54f9dbb5e4a050edddf5862dee29f419c60ceaad052d50aad6fcced5652',
        '0335fec08c867b80e3b71545be195e1b9876b2040d5393fc177b6edca78bde8e42',
        '027e2269d8a770343223bedc49bab31b3c52fb4c1df6627153e6374ac23e2d878b',
        '025b86c5d4cce6de8ea076ac1983e0857da8322b5eb8613928d3ec7fdd7229b873',
        '027f997ab2d8874c8f06d12ef78d39e309ed887a1141d2ba0f50aec8abffaffcde',
        '02e311d97f92dc879860ec0d63b344239f17149ed1700b279b5ef52d2baaa0226f',
        '03f6af8c750b9d29d9da3d4ddf5818a1fcdd4558ba0dde731f9c4b17bcbdcd83f2',
        '029918d8fe6a78cc01bbab31f636494568dd954431f75f4ea6ff1da040b7063a70',
        '036a520acf24036ff691a4f8ba19514828e9b5aa36ca4ba0452e9012023caccfef',
    ];

    for ($round = 136669; $round <= 136675; $round++) {
        foreach ($delegatePublicKeysBalanceDesc as $key => $publicKey) {
            Round::factory()->create([
                'round'      => strval($round),
                'public_key' => $publicKey,
                'balance'    => (100 - $key), // so that first one will have higher balance
            ]);

            $height = ($round - 1) * 51 + $key;
            Block::factory()->create([
                'height'    => $height,
                'timestamp' => $height * 8,
            ]);
        }
    }
});

it('should execute the command - with parameters', function () {
    Artisan::call('explorer:forging-stats:build', [
        '--height' => 6970364, '--days' => 0.01,
    ]);

    $this->assertEquals(ForgingStats::all()->count(), 153);
});

it('should execute the command - without parameter', function () {
    Artisan::call('explorer:forging-stats:build');

    $this->assertEquals(ForgingStats::all()->count(), 50);
    // because of how test data is generated (see beforeEach), the last round contains
    // 50 blocks, hence 50 entries in forging stats
});

it('should not add multiple multiple records to database', function () {
    $args = [
        '--height' => 6970364, '--days' => 0.01,
    ];

    Artisan::call('explorer:forging-stats:build', $args);
    Artisan::call('explorer:forging-stats:build', $args);
    Artisan::call('explorer:forging-stats:build', $args);
    Artisan::call('explorer:forging-stats:build', $args);
    Artisan::call('explorer:forging-stats:build', $args);

    $this->assertEquals(ForgingStats::all()->count(), 153);
});

it('should store the height for missed blocks', function () {
    Block::factory()->create([
        'timestamp' => 45000,
        'height'    => 20,
    ]);

    MissedBlocksCalculator::shouldReceive('calculateFromHeightGoingBack')
        ->once()
        ->andReturn([
            50000 => [
                'publicKey' => 'test-public-key',
                'forged'    => false,
            ],
            50001 => [
                'publicKey' => 'test-public-key-2',
                'forged'    => false,
            ],
        ]);

    Artisan::call('explorer:forging-stats:build', ['--height' => 20]);

    $forgingStats = ForgingStats::where('forged', false)->get();

    expect($forgingStats->count())->toBe(2);
    expect($forgingStats->get(0)->missed_height)->toBe(21);
    expect($forgingStats->get(1)->missed_height)->toBe(21);
});

it('should batch upsert every 1000 records', function () {
    Block::factory()->create([
        'timestamp' => 45000,
        'height'    => 20,
    ]);

    $calculations = [];
    foreach (range(50001, 60000) as $index => $height) {
        $calculations[$height] = [
            'publicKey' => 'test-public-key-'.$index,
            'forged'    => false,
        ];
    }

    MissedBlocksCalculator::shouldReceive('calculateFromHeightGoingBack')
        ->once()
        ->andReturn($calculations);

    Artisan::call('explorer:forging-stats:build', ['--height' => 20]);

    $forgingStats = ForgingStats::where('forged', false)->get();

    expect($forgingStats->count())->toBe(10000);
});
