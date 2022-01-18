<?php

declare(strict_types=1);

use App\Console\Commands\ClearExpiredViews;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    $viewPath = Config::get('view.compiled');

    $this->files = collect([
        $viewPath.'/view1.blade.php',
        $viewPath.'/view2.blade.php',
        $viewPath.'/view3.blade.php',
    ]);

    $this->files->each(fn (string $path) => file_put_contents($path, 'Contents'));
});

afterEach(function () {
    $this->files->each(function (string $path) {
        if (file_exists($path)) {
            unlink($path);
        }
    });
});

it('clears only the views that are older than the expiration time', function () {
    $expiresMinutes = ClearExpiredViews::EXPIRES_MINUTES;

    // Updated the expiration time ago + 1 minute
    touch($this->files[0], Carbon::now()->subMinutes($expiresMinutes + 1)->timestamp);

    // Updated exactly the expiration time ago
    touch($this->files[1], Carbon::now()->subMinutes($expiresMinutes)->timestamp);

    // Updated right now
    touch($this->files[2], Carbon::now()->timestamp);

    expect(file_exists($this->files[0]))->toBeTrue();
    expect(file_exists($this->files[1]))->toBeTrue();
    expect(file_exists($this->files[2]))->toBeTrue();

    Artisan::call('view:clear-expired');

    expect(file_exists($this->files[0]))->toBeFalse();
    expect(file_exists($this->files[1]))->toBeTrue();
    expect(file_exists($this->files[2]))->toBeTrue();
});
