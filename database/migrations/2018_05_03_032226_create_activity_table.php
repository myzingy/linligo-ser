<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('uid');
            $table->string('province',10)->comment('省');
            $table->string('city',10)->comment('市');
            $table->string('region',20)->comment('小区名');
            $table->timestamp('stime')->comment('开始时间')->nullable();
            $table->timestamp('etime')->comment('结束时间')->nullable();
            $table->string('goto')->comment('去往');
            $table->string('distribution')->comment('配送方式');
            $table->string('types')->comment('采购类型');
            $table->integer('commission')->comment('佣金');
            $table->tinyInteger('status');
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
        Schema::dropIfExists('activity');
    }
}
