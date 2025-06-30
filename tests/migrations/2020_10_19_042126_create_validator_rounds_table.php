<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up()
    {
        Schema::create('validator_rounds', function (Blueprint $table) {
            $table->unsignedBigInteger('round')->primary();
            $table->unsignedBigInteger('round_height')->unique();
            $table->jsonb('validators');
            $table->jsonb('votes');
        });
    }
};
