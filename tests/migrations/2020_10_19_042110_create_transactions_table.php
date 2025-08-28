<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->string('hash');
            $table->string('block_hash');
            $table->string('block_number');
            $table->integer('transaction_index');
            $table->unsignedBigInteger('timestamp');
            $table->unsignedBigInteger('nonce');
            $table->string('sender_public_key');
            $table->string('from');
            $table->string('to')->nullable();
            $table->addColumn('numeric', 'value');
            $table->addColumn('numeric', 'gas_price');
            $table->integer('gas');
            $table->binary('data')->nullable();
            $table->string('signature')->nullable();
            $table->string('legacy_second_signature')->nullable();
            $table->boolean('status');
            $table->addColumn('numeric', 'gas_used');
            $table->addColumn('numeric', 'gas_refunded');
            $table->string('deployed_contract_address')->nullable();
            $table->jsonb('logs')->nullable();
            $table->binary('output')->nullable();
            $table->timestamps();
        });
    }
};
