<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateReceiptsTable extends Migration
{
    public function up()
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->string('transaction_hash');
            $table->boolean('success');
            $table->unsignedBigInteger('block_number');
            $table->addColumn('numeric', 'gas_used');
            $table->addColumn('numeric', 'gas_refunded');
            $table->string('contract_address')->nullable();
            $table->jsonb('logs')->nullable();
            $table->binary('output')->nullable();
        });
    }
}
