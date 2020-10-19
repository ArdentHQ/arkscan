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
            $table->id();
            $table->unsignedBigInteger('block_id');
            $table->string('sender_public_key');
            $table->string('recipient_id');
            $table->unsignedBigInteger('timestamp');
            $table->unsignedBigInteger('amount');
            $table->unsignedBigInteger('fee');
            $table->binary('vendor_field_hex')->nullable();
            $table->timestamps();
        });
    }
}
