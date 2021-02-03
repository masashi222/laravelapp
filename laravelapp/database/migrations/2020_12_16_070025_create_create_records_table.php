<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreateRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('create_records', function (Blueprint $table) {
            $table->integerIncrements('shift_createid')->unsigned();
            $table->smallInteger('memberid')->unsigned();
            $table->dateTime('go_time');
            $table->dateTime('out_time');
            $table->unsignedtinyInteger('is_register')->nullable();
            $table->unsignedtinyinteger('is_post');
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

        Schema::dropIfExists('create_records');
    }
}
