<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserBalanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usrBalances', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('usrCustomer_id')->nullable()->index();
            $table->float('nab', 8, 4)->nullable();
            $table->float('balance', 20, 2)->nullable();
            $table->float('unit', 20, 4)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usrBalances');
    }
}
