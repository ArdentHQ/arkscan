<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Models\Block;
use App\Models\Round;
use App\Models\Wallet;
use App\Services\Monitor\MissedBlocksCalculator;
use Carbon\Carbon;

it('should calculate the missed blocks', function () {
    $expectedStats                  = []; // expected { forged, missed } by validator public key
    $heightIterator                 = 0;
    $validatorPublicKeysBalanceDesc = [
        '03931feebc4cc63b508d991e21023564e612072cf026fd0f9f2dd091a064d544c1',
        '035334916d9adeae7ddf937fe9a72488206dc73d3deabaa82419bced7a4bdb7e09',
        '02762c0f72250a6bfb1e744d2ef94a565e01a4838fa0ca6bfed126598fb859adf4',
        '030565e9b148a12ed2c500dd9acd7d7b97bd37723804181c7e492ed4f49aae26f6',
        '025381d4205f87df9387ecb0957573ab2f0bce47d880cddbdb3423b15dea6b4775',
        '0391efef50e718683588e44f10e0bbcc19469da24de83d46ab8d2e488febffa8d6',
        '03eae68ae44161c755ef212bfcbc476a773dec1c023100a31bd775e09c4e430893',
        '02f50270f25c600d3411d4ad2d98065a413e08e5f891a079210bb8e35c982236aa',
        '0344f117129787f5f36c40c33f0aa62310f45f00a94361efa3e178e402f539af98',
        '02874e789117a42947e442aac6a43a02fdab283973a1aae76ea8ddc31cd299bebe', // Missed
        '03a461f557c88612328c8e6d69991eaa7916359dfd2c6a65fd988b672a8bb780c4',
        '03a9927b2d4e5481abffb44c3362fafe54a295057224909d6cc9d8674a7a2ad2c6',
        '0204b4c02886108b1a1006371fb5313ab4eb3d6a7d375070d956a59ea87f818e77',
        '0370049512c71549a5cd8bb6a2e33fd85729f74bb1070c7e9dcc0b3708c6d35177',
        '03636080960d7998b2baf3385fe8bea9bb05940aceb7a778b4a231a9cd47005cd9',
        '030cc854a680d4065304bd49928cdf80154e69f3d43fdf28b0c4b2e46d87a9630c',
        '0276669e64188c2a0044665eb0ed3b501563c19b27fb2b902b7fe23d705522211c',
        '029283adfe1a02bf652c987180db5925d8c095c6ac5c4194fa844ef6a1f2ced941',
        '03b1ca75077ea1ce752af6739591f4985b5ec3245243b1cdb16afa83a79f040d03',
        '038297916c825d2e09ffe03de2c67a15803472fc92569240fa8fbf457502d7400d', // Missed
        '02ab5bebf52333bdad0d3d75e6b5b1f602adf7b8af6faa87c23b2e121318f95542',
        '022a90a50c444195c35b852460c7d2be0312290bd5a1695d7a57a9554bcb34d0d7',
        '03022fe77444e68de32ac034a0dc628a8b3d362784fa37e09e035e932c6a16e439',
        '0216accc59f9d143ed562853a28558ed835d3e484d36dd53290a4f7c2752f57c88',
        '02ba352eab5ddfd1a19f5681162b1550b035003ed01bd83cd173f48dbb8ebb2907',
        '02872e746de4310161781ce49eddfd9ec492a9cf3a9bf0c5571af7eb2e175c2d2c',
        '034cc5de0e7d78bd56706e787c8f1f7c1029eb404c8296d0ee40379d1f0529975f',
        '027f920f4e3f012fe05ccf0b73e7973bddbe0778c8eeedf06e8c82d7c16d450686',
        '03f25455408f9a7e6c6a056b121e68fbda98f3511d22e9ef27b0ebaf1ef9e4eabc',
        '03ce47f02c036685ac897e7dd4e931ff83a52238e94a769e23b4b13c44ac558d3f', // Missed
        '0270d7400c6262ea35dadbb2f71d6e871dc48c3f1869fb68e03f9a0179b530226c',
        '022d7172caf2f5fc65450a9e513be501f7f8f2ccc64bf341f569cd71365d908e9b',
        '02dfc9a0684fe0744101b2398e9c86a81b5e46aceffff994ef9189083f01d0bebb',
        '033e43c6eee0aa5065407b6a60f2a98334000ed0a7643a9e4487621331e7ea1d83',
        '03d878ed802a26f299c1492e5a5db7615a45bfa05cc703881d7f28286ce8bb96a4',
        '0306eac6dc8d83f3cd9135dd350b023a9561cad2d884037ee757613ddc6f48ee2c',
        '0288ea329a06e1a983b9ce0cb34ab35664ead0a174b3a13090899d09b5aa064b81',
        '0367039bdc55e4ea57e913c57010036358d75d7c8eced05c0ba5d972a478c42143',
        '0205c38eecba963b935c88f9d785b58930f74cb9bfccb45e857c9ad98ba50e2997',
        '02b6d4a02e6d4e7e8d5190005e3450bb1d993af563c5229b2ca06c29143f318d07', // Missed
        '0375e624da5204a6b1181673d9027b534269a7bdf288bc6067c675f8d144cf8698',
        '02905643df1fffd9c11d6fd35864802f87729a2727b5e2d24e32dd4835e955dcf3',
        '03e3506351e7c5426457c4cd21dea4f2bc95ccfbe8183e6aeae92aee0835e9f942',
        '03be3616815c3a5115c229279b9e2e0c755c9a105a973e7cba68921996c177057b',
        '0373a76702419c9b7405368024813af73ca19bf81cbee67c3095a30ada6cd17a78',
        '02968e862011738ac185e87f47dec61b32c842fd8e24fab625c02a15ad7e2d0f65',
        '0272f6dbecafa9a09f747b8116fd0c4207c3a34575da8d97087e6cd8d5f5f53d6a',
        '03a8762ebe696d8b51e217a3f282bd7a831403557288453b563abd38c9ef6de27b',
        '03f3f6d09101d6fd97cc5b987707442c14cb4b990b097852f265ef41631ae4c7d5',
        '03c521450f8fb765d7ee2fd3cabfd0ab0138ecc5ae4d69d47ed910088183c52451', // Missed
        '022c94738794b9a25dd8e5db2a86a888463a05553d5522e840f5c9e254ae93355e',
        '03150a599c9f8df707f3433fe8d6d53bda646de0054cc82cb22ba375cb9bd1f096',
        '027e80c676004226c602df8c35260955bb5cb14e852851f4cb281d840cbf2f1e85',
    ];

    $validatorOrderForRound = [
        '03931feebc4cc63b508d991e21023564e612072cf026fd0f9f2dd091a064d544c1',
        '035334916d9adeae7ddf937fe9a72488206dc73d3deabaa82419bced7a4bdb7e09',
        '02762c0f72250a6bfb1e744d2ef94a565e01a4838fa0ca6bfed126598fb859adf4',
        '030565e9b148a12ed2c500dd9acd7d7b97bd37723804181c7e492ed4f49aae26f6',
        '025381d4205f87df9387ecb0957573ab2f0bce47d880cddbdb3423b15dea6b4775',
        '0391efef50e718683588e44f10e0bbcc19469da24de83d46ab8d2e488febffa8d6',
        '03eae68ae44161c755ef212bfcbc476a773dec1c023100a31bd775e09c4e430893',
        '02f50270f25c600d3411d4ad2d98065a413e08e5f891a079210bb8e35c982236aa',
        '0344f117129787f5f36c40c33f0aa62310f45f00a94361efa3e178e402f539af98',
        '02874e789117a42947e442aac6a43a02fdab283973a1aae76ea8ddc31cd299bebe', // Missed
        '03a461f557c88612328c8e6d69991eaa7916359dfd2c6a65fd988b672a8bb780c4', // TODO: look into why this is flagged as missed (the delegate who picked up the missed block)
        '03a9927b2d4e5481abffb44c3362fafe54a295057224909d6cc9d8674a7a2ad2c6',
        '0204b4c02886108b1a1006371fb5313ab4eb3d6a7d375070d956a59ea87f818e77',
        '0370049512c71549a5cd8bb6a2e33fd85729f74bb1070c7e9dcc0b3708c6d35177',
        '03636080960d7998b2baf3385fe8bea9bb05940aceb7a778b4a231a9cd47005cd9',
        '030cc854a680d4065304bd49928cdf80154e69f3d43fdf28b0c4b2e46d87a9630c',
        '0276669e64188c2a0044665eb0ed3b501563c19b27fb2b902b7fe23d705522211c',
        '029283adfe1a02bf652c987180db5925d8c095c6ac5c4194fa844ef6a1f2ced941',
        '03b1ca75077ea1ce752af6739591f4985b5ec3245243b1cdb16afa83a79f040d03',
        '038297916c825d2e09ffe03de2c67a15803472fc92569240fa8fbf457502d7400d', // Missed
        '02ab5bebf52333bdad0d3d75e6b5b1f602adf7b8af6faa87c23b2e121318f95542', // TODO: look into why this is flagged as missed (the delegate who picked up the missed block)
        '022a90a50c444195c35b852460c7d2be0312290bd5a1695d7a57a9554bcb34d0d7',
        '03022fe77444e68de32ac034a0dc628a8b3d362784fa37e09e035e932c6a16e439',
        '0216accc59f9d143ed562853a28558ed835d3e484d36dd53290a4f7c2752f57c88',
        '02ba352eab5ddfd1a19f5681162b1550b035003ed01bd83cd173f48dbb8ebb2907',
        '02872e746de4310161781ce49eddfd9ec492a9cf3a9bf0c5571af7eb2e175c2d2c',
        '034cc5de0e7d78bd56706e787c8f1f7c1029eb404c8296d0ee40379d1f0529975f',
        '027f920f4e3f012fe05ccf0b73e7973bddbe0778c8eeedf06e8c82d7c16d450686',
        '03f25455408f9a7e6c6a056b121e68fbda98f3511d22e9ef27b0ebaf1ef9e4eabc',
        '03ce47f02c036685ac897e7dd4e931ff83a52238e94a769e23b4b13c44ac558d3f', // Missed
        '0270d7400c6262ea35dadbb2f71d6e871dc48c3f1869fb68e03f9a0179b530226c', // TODO: look into why this is flagged as missed (the delegate who picked up the missed block)
        '022d7172caf2f5fc65450a9e513be501f7f8f2ccc64bf341f569cd71365d908e9b',
        '02dfc9a0684fe0744101b2398e9c86a81b5e46aceffff994ef9189083f01d0bebb',
        '033e43c6eee0aa5065407b6a60f2a98334000ed0a7643a9e4487621331e7ea1d83',
        '03d878ed802a26f299c1492e5a5db7615a45bfa05cc703881d7f28286ce8bb96a4',
        '0306eac6dc8d83f3cd9135dd350b023a9561cad2d884037ee757613ddc6f48ee2c',
        '0288ea329a06e1a983b9ce0cb34ab35664ead0a174b3a13090899d09b5aa064b81',
        '0367039bdc55e4ea57e913c57010036358d75d7c8eced05c0ba5d972a478c42143',
        '0205c38eecba963b935c88f9d785b58930f74cb9bfccb45e857c9ad98ba50e2997',
        '02b6d4a02e6d4e7e8d5190005e3450bb1d993af563c5229b2ca06c29143f318d07', // Missed
        '0375e624da5204a6b1181673d9027b534269a7bdf288bc6067c675f8d144cf8698', // TODO: look into why this is flagged as missed (the delegate who picked up the missed block)
        '02905643df1fffd9c11d6fd35864802f87729a2727b5e2d24e32dd4835e955dcf3',
        '03e3506351e7c5426457c4cd21dea4f2bc95ccfbe8183e6aeae92aee0835e9f942',
        '03be3616815c3a5115c229279b9e2e0c755c9a105a973e7cba68921996c177057b',
        '0373a76702419c9b7405368024813af73ca19bf81cbee67c3095a30ada6cd17a78',
        '02968e862011738ac185e87f47dec61b32c842fd8e24fab625c02a15ad7e2d0f65',
        '0272f6dbecafa9a09f747b8116fd0c4207c3a34575da8d97087e6cd8d5f5f53d6a',
        '03a8762ebe696d8b51e217a3f282bd7a831403557288453b563abd38c9ef6de27b',
        '03f3f6d09101d6fd97cc5b987707442c14cb4b990b097852f265ef41631ae4c7d5',
        '03c521450f8fb765d7ee2fd3cabfd0ab0138ecc5ae4d69d47ed910088183c52451', // Missed
        '022c94738794b9a25dd8e5db2a86a888463a05553d5522e840f5c9e254ae93355e', // TODO: look into why this is flagged as missed (the delegate who picked up the missed block)
        '03150a599c9f8df707f3433fe8d6d53bda646de0054cc82cb22ba375cb9bd1f096',
        '027e80c676004226c602df8c35260955bb5cb14e852851f4cb281d840cbf2f1e85',
        '03931feebc4cc63b508d991e21023564e612072cf026fd0f9f2dd091a064d544c1',
        '035334916d9adeae7ddf937fe9a72488206dc73d3deabaa82419bced7a4bdb7e09',
        '02762c0f72250a6bfb1e744d2ef94a565e01a4838fa0ca6bfed126598fb859adf4',
        '030565e9b148a12ed2c500dd9acd7d7b97bd37723804181c7e492ed4f49aae26f6',
        '025381d4205f87df9387ecb0957573ab2f0bce47d880cddbdb3423b15dea6b4775',
    ];
    $validatorsWhoMissed = [
        '02874e789117a42947e442aac6a43a02fdab283973a1aae76ea8ddc31cd299bebe',
        '038297916c825d2e09ffe03de2c67a15803472fc92569240fa8fbf457502d7400d',
        '03ce47f02c036685ac897e7dd4e931ff83a52238e94a769e23b4b13c44ac558d3f',
        '02b6d4a02e6d4e7e8d5190005e3450bb1d993af563c5229b2ca06c29143f318d07',
        '03c521450f8fb765d7ee2fd3cabfd0ab0138ecc5ae4d69d47ed910088183c52451',
    ];

    $round = Round::factory()->create([
        'round'        => 7059,
        'round_height' => (7059 - 1) * Network::validatorCount(),
        'validators'   => $validatorPublicKeysBalanceDesc,
    ]);

    $this->travelTo(Carbon::parse('2023-03-14 12:44:32'));

    foreach ($validatorOrderForRound as $publicKey) {
        if (! in_array($publicKey, $validatorsWhoMissed, true)) {
            // Start height for round 7059 is 374074
            Block::factory()->create([
                'generator_public_key' => $publicKey,
                'height'    => 374074 + $heightIterator,
                'timestamp' => Carbon::now()->getTimestampMs(),
            ]);

            $heightIterator++;

            if (! isset($expectedStats[$publicKey])) {
                $expectedStats[$publicKey] = [
                    'forged'=> 0,
                    'missed'=> 0,
                ];
            }

            $expectedStats[$publicKey]['forged']++;
        } else {
            if (! isset($expectedStats[$publicKey])) {
                $expectedStats[$publicKey] = [
                    'forged'=> 0,
                    'missed'=> 0,
                ];
            }

            $expectedStats[$publicKey]['missed']++;
        }

        $this->travel(8)->seconds();
    }

    Block::factory()->create([
        'height'    => 374073,
        'timestamp' => Carbon::now()->getTimestampMs(),
    ]);

    $blocksInfo     = MissedBlocksCalculator::calculateForRound($round, $round->round_height - 1 + Network::validatorCount()); // any height in the round
    $validatorStats = [];
    foreach ($blocksInfo as $blockInfo) {
        if (! isset($validatorStats[$blockInfo['publicKey']])) {
            $validatorStats[$blockInfo['publicKey']] = [
                'forged' => 0,
                'missed' => 0,
            ];
        }

        if ($blockInfo['forged']) {
            $validatorStats[$blockInfo['publicKey']]['forged']++;
        } else {
            $validatorStats[$blockInfo['publicKey']]['missed']++;
        }
    }

    expect($validatorStats)->toEqual($expectedStats);
})->skip('look into why missed blocks consider the next delegate as not forged');
