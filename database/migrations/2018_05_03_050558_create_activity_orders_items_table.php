<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityOrdersItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_orders_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('act_id')->comment('activity_id');
            $table->integer('order_id');
            $table->string('name',10);
            $table->integer('weight')->comment('精确到g');
            $table->string('weight_unit',10);
            $table->integer('actual_weight')->comment('实际重量');
            $table->integer('price');
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
        Schema::dropIfExists('activity_orders_items');
    }
}
