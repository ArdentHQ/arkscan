<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateBlocksTable extends Migration
{
    public function up()
    {
        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('previous_block');
            $table->unsignedBigInteger('height');
            $table->unsignedBigInteger('timestamp');
            $table->unsignedBigInteger('totalAmount');
            $table->unsignedBigInteger('totalFee');
            $table->unsignedBigInteger('reward');
            $table->timestamps();
        });
    }
}
