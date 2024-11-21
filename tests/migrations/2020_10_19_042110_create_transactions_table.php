<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->string('id');
            $table->string('block_id');
            $table->string('block_height');
            $table->string('sender_public_key');
            $table->string('recipient_id')->nullable();
            $table->unsignedBigInteger('timestamp');
            $table->addColumn('numeric', 'amount');
            $table->addColumn('numeric', 'gas_price');
            $table->unsignedBigInteger('nonce');
            $table->binary('data')->nullable();
            $table->jsonb('asset')->nullable();
            $table->timestamps();
        });
    }
}
