<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Facades\Network;
use App\Services\BigNumber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

final class GenerateVoteReport implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The command dispatches this job every five minutes, so we only attempt it once per schedule tick.
     */
    public int $tries = 1;

    public function handle(): void
    {
        $api            = Network::api();
        $validatorCount = Network::validatorCount();

        $validatorsPage1 = Http::get("{$api}/validators", ['page' => 1, 'limit' => $validatorCount])->json();
        $validatorsPage2 = Http::get("{$api}/validators", ['page' => 2, 'limit' => $validatorCount])->json();

        if ($validatorsPage1 === null || $validatorsPage2 === null) {
            return;
        }

        $supply = Http::get("{$api}/blockchain")->json('data.supply');
        if ($supply === null) {
            return;
        }

        $activeValidators = $validatorsPage1['data'] ?? [];

        $totalVotes  = BigNumber::new('0');
        $totalVoters = 0;

        foreach ($activeValidators as $validator) {
            $totalVotes->plus($validator['attributes']['validatorVoteBalance'] ?? '0');
            $totalVoters += (int) ($validator['attributes']['validatorVotersCount'] ?? 0);
        }

        $percentage = bcmul(
            bcdiv((string) $totalVotes, (string) $supply, 4),
            '100',
            2,
        );

        $formattedTotalVotes = number_format($totalVotes->toFloat(), 0, '.', ',');
        $formattedSupply     = number_format(BigNumber::new($supply)->toFloat(), 0, '.', ',');

        $lines   = [];
        $lines[] = '';
        $lines[] = sprintf('Top %d Validators Stats', $validatorCount);
        $lines[] = '';
        $lines[] = sprintf('=> Total Votes  : %s%% ( %s / %s )', $percentage, $formattedTotalVotes, $formattedSupply);
        $lines[] = sprintf('=> Total Voters : %d', $totalVoters);
        $lines[] = '';

        $header    = sprintf('| %s | %s | %s | %s | %s |', 'Rank', str_pad('Validator', 42), 'Vote %', str_pad('Vote ARK', 15), 'Voters');
        $separator = str_repeat('=', strlen($header));

        $lines[] = $separator;
        $lines[] = $header;
        $lines[] = $separator;

        foreach ($activeValidators as $validator) {
            $lines[] = $this->formatValidatorLine($validator);
        }

        $lines[] = $separator;

        foreach ($validatorsPage2['data'] ?? [] as $validator) {
            $lines[] = $this->formatValidatorLine($validator);
        }

        $lines[] = $separator;
        $lines[] = '';
        $lines[] = sprintf(' %s UTC / VoteReport / ark.io ', now()->utc()->format('Y-m-d H:i:s'));
        $lines[] = '';
        $lines[] = sprintf(' NOTE: This report is based on the %d active validators only! ', $validatorCount);
        $lines[] = '';

        file_put_contents(public_path('VoteReport.txt'), implode("\n", $lines));
    }

    /**
     * @param array<string, mixed> $validator
     */
    private function formatValidatorLine(array $validator): string
    {
        $attributes = $validator['attributes'] ?? [];

        $rank     = str_pad((string) ($attributes['validatorRank'] ?? 0), 4, ' ', STR_PAD_LEFT);
        $address  = str_pad($validator['address'] ?? '', 42);
        $approval = str_pad(number_format((float) ($attributes['validatorApproval'] ?? 0), 2), 6, ' ', STR_PAD_LEFT);
        $votes    = str_pad(number_format(BigNumber::new($attributes['validatorVoteBalance'] ?? '0')->toFloat(), 0, '.', ','), 15, ' ', STR_PAD_LEFT);
        $voters   = str_pad((string) ($attributes['validatorVotersCount'] ?? 0), 6, ' ', STR_PAD_LEFT);

        return sprintf('| %s | %s | %s | %s | %s |', $rank, $address, $approval, $votes, $voters);
    }
}
