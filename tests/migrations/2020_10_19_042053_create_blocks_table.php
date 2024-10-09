<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Grammars\PostgresGrammar;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Fluent;

final class CreateBlocksTable extends Migration
{
    public function up()
    {
        PostgresGrammar::macro('typeNumeric', function (Fluent $column) {
            return 'numeric';
        });

        Schema::create('blocks', function (Blueprint $table) {
            $table->string('id');
            $table->unsignedBigInteger('version');
            $table->unsignedBigInteger('timestamp');
            $table->string('previous_block')->nullable();
            $table->unsignedBigInteger('height');
            $table->unsignedBigInteger('number_of_transactions');
            $table->addColumn('numeric', 'total_amount');
            $table->addColumn('numeric', 'total_fee');
            $table->integer('total_gas_used');
            $table->addColumn('numeric', 'reward');
            $table->unsignedBigInteger('payload_length');
            $table->string('payload_hash');
            $table->string('generator_address');
            $table->string('block_signature');
            $table->timestamps();
        });
    }
}
