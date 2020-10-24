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
            $table->string('id');
            $table->unsignedBigInteger('version');
            $table->unsignedBigInteger('timestamp');
            $table->string('previous_block')->nullable();
            $table->unsignedBigInteger('height');
            $table->unsignedBigInteger('number_of_transactions');
            $table->unsignedBigInteger('total_amount');
            $table->unsignedBigInteger('total_fee');
            $table->unsignedBigInteger('reward');
            $table->unsignedBigInteger('payload_length');
            $table->string('payload_hash');
            $table->string('generator_public_key');
            $table->string('block_signature');
            $table->timestamps();
        });
    }
}
