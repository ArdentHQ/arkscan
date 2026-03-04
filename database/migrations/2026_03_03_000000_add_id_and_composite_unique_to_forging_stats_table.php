<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class AddIdAndCompositeUniqueToForgingStatsTable extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            Schema::create('forging_stats_new', function (Blueprint $table) {
                $table->id();
                $table->integer('timestamp');
                $table->string('address');
                $table->boolean('forged');
                $table->integer('missed_height')->nullable();
                $table->timestamps();
                $table->unique(['timestamp', 'address']);
            });

            DB::statement('INSERT INTO forging_stats_new (timestamp, address, forged, missed_height, created_at, updated_at) SELECT timestamp, address, forged, missed_height, created_at, updated_at FROM forging_stats');

            Schema::drop('forging_stats');
            Schema::rename('forging_stats_new', 'forging_stats');

            return;
        }

        Schema::table('forging_stats', function (Blueprint $table) {
            $table->dropPrimary('forging_stats_pkey');
        });

        Schema::table('forging_stats', function (Blueprint $table) {
            $table->id()->first();
            $table->unique(['timestamp', 'address']);
        });
    }
}
