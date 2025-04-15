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
            $table->string('hash');
            $table->integer('version');
            $table->integer('timestamp');
            $table->string('parent_hash')->nullable();
            $table->string('state_root')->nullable();
            $table->unsignedBigInteger('number');
            $table->unsignedBigInteger('transactions_count');
            $table->integer('gas_used');
            $table->addColumn('numeric', 'amount');
            $table->addColumn('numeric', 'fee');
            $table->addColumn('numeric', 'reward');
            $table->unsignedBigInteger('payload_size');
            $table->string('transactions_root');
            $table->string('proposer');
            $table->string('block_signature');
            $table->timestamps();
        });
    }
}
