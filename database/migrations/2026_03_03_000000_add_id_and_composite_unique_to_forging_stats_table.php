<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class AddIdAndCompositeUniqueToForgingStatsTable extends Migration
{
    public function up(): void
    {
        Schema::table('forging_stats', function (Blueprint $table) {
            $table->dropPrimary('forging_stats_pkey');
        });

        Schema::table('forging_stats', function (Blueprint $table) {
            $table->id()->first();
            $table->unique(['timestamp', 'address']);
        });
    }
}
