<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('member_records', function (Blueprint $table) {
            $table->smallIncrements('id')->unsigned();
            $table->unsignedtinyInteger('number')->nullable();
            $table->string('name');
            $table->string('pass');
            $table->unsignedSmallInteger('salary');
            $table->unsignedSmallInteger('expense')->nullable();
            $table->unsignedTinyInteger('business_no');
            $table->timestamps();
            $table->foreign('business_no')->references('business_no')->on('business_masters');
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
        Schema::dropIfExists('member_records');
    }
}
