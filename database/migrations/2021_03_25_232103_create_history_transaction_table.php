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
            $table->bigInteger('balanceBefore')->nullable();
            $table->bigInteger('balanceAfter')->nullable();
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
