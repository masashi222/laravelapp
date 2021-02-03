<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('shift_records', function (Blueprint $table) {
            $table->integerIncrements('shift_recordid')->unsigned();
            $table->unsignedInteger('shift_createid');
            $table->unsignedSmallInteger('memberid');
            $table->dateTime('go_time');
            $table->datetime('out_time');
            $table->timestamps();
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
        Schema::dropIfExists('shift_records');
    }
}
