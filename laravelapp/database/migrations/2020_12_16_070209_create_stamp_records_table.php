<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStampRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('stamp_records', function (Blueprint $table) {
            $table->integerIncrements('time_stampid')->unsigned();
            $table->unsignedSmallInteger('memberid');
            $table->dateTime('go_time');
            $table->dateTime('break_in')->nullable();
            $table->dateTime('break_out')->nullable();
            $table->dateTime('out_time')->nullable();
            $table->unsignedSmallInteger('expense');
            $table->timestamps();
            $table->unsignedTinyInteger('go_flg')->default('0');
            $table->unsignedTinyInteger('in_flg')->default('0');
            $table->unsignedTinyInteger('fin_flg')->default('0');
            $table->unsignedTinyInteger('out_flg')->default('0');
            $table->foreign('memberid')->references('id')->on('member_records');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('stamp_records');
    }
}
