<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimeRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('time_records', function (Blueprint $table) {
            $table->integerIncrements('time_recordid')->unsigned();
            $table->unsignedSmallInteger('memberid');
            $table->unsignedTinyInteger('go_days')->nullable();
            $table->time('normaly_worktime')->nullable();
            $table->time('midnight_worktime')->nullable();
            $table->unsignedInteger('normaly_salary')->nullable();
            $table->unsignedInteger('midnight_salary')->nullable();
            $table->unsignedInteger('salary')->nullable();
            $table->unsignedInteger('expense')->nullable();
            $table->unsignedinteger('fixed_salary')->nullable();
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
        Schema::dropIfExists('time_records');
    }
}
