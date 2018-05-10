<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityPurchaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_purchase', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('act_id')->comment('activity_id')->nullable();
            $table->string('name')->comment('商品名')->nullable();
            $table->integer('weight')->comment('总重')->nullable();
            $table->integer('price')->comment('总价')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->text('items');
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
        Schema::dropIfExists('activity_purchase');
    }
}
