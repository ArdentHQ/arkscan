<?php

declare(strict_types=1);

use App\Models\ForgingStats;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up()
    {
        ForgingStats::truncate();

        Schema::table('forging_stats', function (Blueprint $table) {
            $table->integer('missed_height')->nullable();
        });
    }
};
