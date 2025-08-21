<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up()
    {
        Schema::create('multi_payments', function (Blueprint $table) {
            $table->string('hash');
            $table->smallInteger('log_index');
            $table->string('from');
            $table->string('to');
            $table->addColumn('numeric', 'amount');
            $table->boolean('success');
        });
    }
};
