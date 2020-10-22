<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateRoundsTable extends Migration
{
    public function up()
    {
        Schema::create('rounds', function (Blueprint $table) {
            $table->string('id');
            $table->string('public_key');
            $table->unsignedBigInteger('balance');
            $table->unsignedBigInteger('round');
            $table->timestamps();
        });
    }
}
