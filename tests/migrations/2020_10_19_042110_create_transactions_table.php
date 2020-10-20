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
            $table->string('type');
            $table->string('type_group');
            $table->string('sender_public_key');
            $table->string('recipient_id');
            $table->string('timestamp');
            $table->string('amount');
            $table->string('fee');
            $table->binary('vendor_field_hex')->nullable();
            $table->text('asset')->nullable();
            $table->timestamps();
        });
    }
}
