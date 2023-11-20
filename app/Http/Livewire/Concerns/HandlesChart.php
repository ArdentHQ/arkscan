<?php

namespace App\Http\Livewire\Concerns;

use App\Facades\Settings;

trait HandlesChart
{
    use AvailablePeriods;
    use StatisticsChart;

    public string $refreshInterval = '';

    protected function getListenersHandlesChart(): array
    {
        return [
            'currencyChanged' => '$refresh',
            'themeChanged'    => '$refresh',
            'updateChart'     => '$refresh',
        ];
    }

    public function mountHandlesChart(): void
    {
        $this->refreshInterval = (string) config('arkscan.statistics.refreshInterval', '60');
        $this->period          = $this->defaultPeriod();
    }

    private function mainValueVariation(array $dataset): string
    {
        // Determine difference based on first datapoint
        $initialValue = collect($dataset)->first();
        $currentValue = $this->getPrice(Settings::currency());

        return $initialValue > $currentValue ? 'red' : 'green';
    }
}
