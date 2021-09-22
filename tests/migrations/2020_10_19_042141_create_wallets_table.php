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
            $table->string('public_key')->nullable();
            $table->unsignedBigInteger('balance');
            $table->unsignedBigInteger('nonce');
            $table->json('attributes')->nullable();
            $table->timestamps();
        });
    }
}
