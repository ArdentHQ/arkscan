<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up()
    {
        Schema::create('tokens', function (Blueprint $table) {
            $table->string('address')->primary();
            $table->string('symbol');
            $table->string('name');
            $table->unsignedTinyInteger('decimals');
            $table->addColumn('numeric', 'total_supply');
            $table->string('deployment_hash')->nullable();
        });
    }
};
