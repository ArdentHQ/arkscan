<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table('exchanges', function (Blueprint $table) {
            $table->renameColumn('coingecko_id', 'provider_exchange_id');
        });
    }

    public function down(): void
    {
        Schema::table('exchanges', function (Blueprint $table) {
            $table->renameColumn('provider_exchange_id', 'coingecko_id');
        });
    }
};
