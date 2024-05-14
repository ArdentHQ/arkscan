<?php

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
        Schema::create('core_webhooks', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('token');
            $table->string('host');
            $table->integer('port');
            $table->string('event');
            $table->timestamps();
        });
    }
};
