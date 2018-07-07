<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history', function (Blueprint $table) {
            $table->increments('id');
            //$table->integer('user_id');
            // $table->foreign('user_id')->references('id')->on('users');
            $table->integer('record_id');
            // $table->foreign('record_id')->references('id')->on('records');
            $table->char('operation', 6);
            $table->double('op_value');
            $table->double('value');
            $table->integer('steps');
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
        Schema::dropIfExists('history');
    }
}
