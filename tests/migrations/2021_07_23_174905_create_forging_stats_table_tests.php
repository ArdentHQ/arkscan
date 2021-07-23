<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class CreateForgingStatsTableTests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // If the table exists means was migrated with a old migration that was
        // removed because a bug with the class name:
        // (tests/migrations/2021_03_11_000000_create_forging_stats_table.php)
        if (Schema::hasTable('forging_stats')) {
            // Since the file no longer exist we can remove the entry on the
            // migrations table
            DB::table('migrations')
                ->where('migration', '2021_03_11_000000_create_forging_stats_table')
                ->delete();
            // Since the table exists no more actions needed
            return;
        }

        Schema::create('forging_stats', function (Blueprint $table) {
            $table->integer('timestamp')->primary();
            $table->string('public_key');
            $table->boolean('forged');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
}
