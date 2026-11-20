<?php

declare(strict_types=1);

it('should compile every view', function (): void {
    $this->artisan('view:cache')->assertSuccessful();

    $this->artisan('view:clear')->assertSuccessful();
});
