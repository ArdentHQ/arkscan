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
            $table->unsignedBigInteger('type');
            $table->unsignedBigInteger('type_group');
            $table->string('sender_public_key');
            $table->string('recipient_id')->nullable();
            $table->unsignedBigInteger('timestamp');
            $table->unsignedBigInteger('amount');
            $table->unsignedBigInteger('fee');
            $table->unsignedBigInteger('nonce');
            $table->binary('vendor_field')->nullable();
            $table->jsonb('asset')->nullable();
            $table->timestamps();
        });
    }
}
