<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('exchanges', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('url');
            $table->boolean('is_exchange');
            $table->boolean('is_aggregator');
            $table->boolean('btc');
            $table->boolean('eth');
            $table->boolean('stablecoins');
            $table->boolean('other');
            $table->string('icon');
            $table->string('coingecko_id')->nullable();
            $table->string('price')->nullable();
            $table->string('volume')->nullable();
            $table->timestamps();
        });
    }
};
