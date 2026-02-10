<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up()
    {
        Schema::create('token_transfers', function (Blueprint $table) {
            $table->string('address');
            $table->bigInteger('block_number');
            $table->smallInteger('index');
            $table->string('transaction_hash');
            $table->string('from');
            $table->string('to');
            $table->addColumn('numeric', 'value');

            $table->primary(['address', 'block_number', 'index']);
        });
    }
};
