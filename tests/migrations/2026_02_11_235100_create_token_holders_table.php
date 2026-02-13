<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up()
    {
        Schema::create('token_holders', function (Blueprint $table) {
            $table->string('token_address');
            $table->string('address');
            $table->addColumn('numeric', 'balance');

            $table->primary(['token_address', 'address']);
        });
    }
};
