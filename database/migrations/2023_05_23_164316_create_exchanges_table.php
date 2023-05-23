<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchanges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->boolean('is_exchange');
            $table->boolean('is_aggregator');
            $table->boolean('btc');
            $table->boolean('eth');
            $table->boolean('stablecoins');
            $table->boolean('other');
            $table->string('volume')->nullable();
            $table->timestamps();
        });
    }
};
