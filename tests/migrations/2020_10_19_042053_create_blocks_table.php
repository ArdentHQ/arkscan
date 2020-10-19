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
            $table->string('version');
            $table->string('timestamp');
            $table->string('previous_block');
            $table->string('height');
            $table->string('number_of_transactions');
            $table->string('total_amount');
            $table->string('total_fee');
            $table->string('reward');
            $table->string('payload_length');
            $table->string('payload_hash');
            $table->string('generator_public_key');
            $table->string('block_signature');
            $table->timestamps();
        });
    }
}
