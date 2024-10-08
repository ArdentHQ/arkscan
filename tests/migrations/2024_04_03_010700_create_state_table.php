<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateStateTable extends Migration
{
    public function up()
    {
        Schema::create('state', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->unsignedBigInteger('height');
            $table->addColumn('numeric', 'supply');
        });
    }
}
