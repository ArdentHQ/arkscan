<?php

use App\Models\ForgingStats;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        ForgingStats::truncate();

        Schema::table('forging_stats', function (Blueprint $table) {
            $table->dropColumn('public_key');
            // $table->string('public_key')->nullable()->change();
            $table->string('address');
        });

        // dd('yo');
    }
};
