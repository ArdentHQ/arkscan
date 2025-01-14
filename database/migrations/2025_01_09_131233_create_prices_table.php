<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->timestamp('timestamp');
            $table->string('currency');
            $table->decimal('value', 20, 14);

            $table->unique(['currency', 'timestamp'], 'prices_currency_timestamp_unique_index');
        });
    }
};
