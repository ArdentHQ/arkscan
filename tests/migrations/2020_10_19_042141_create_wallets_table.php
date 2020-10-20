<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateWalletsTable extends Migration
{
    public function up()
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->string('id');
            $table->string('address');
            $table->string('public_key');
            $table->string('second_public_key');
            $table->string('vote')->nullable();
            $table->string('username')->nullable();
            $table->string('balance');
            $table->string('nonce');
            $table->string('vote_balance');
            $table->string('produced_blocks')->nullable();
            $table->string('missed_blocks')->nullable();
            $table->timestamps();
        });
    }
}
