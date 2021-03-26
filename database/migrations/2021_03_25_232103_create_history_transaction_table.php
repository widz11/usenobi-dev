<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hisTransactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('usrCustomer_id')->nullable()->index();
            $table->float('nab', 8, 4)->nullable();
            $table->float('balanceInOut', 20, 2)->nullable();
            $table->float('balanceBefore', 20, 2)->nullable();
            $table->float('balanceAfter', 20, 2)->nullable();
            $table->float('unitBefore', 20, 4)->nullable();
            $table->float('unitAfter', 20, 4)->nullable();
            $table->string('description')->nullable();
            $table->string('type')->nullable();
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
        Schema::dropIfExists('hisTransactions');
    }
}
