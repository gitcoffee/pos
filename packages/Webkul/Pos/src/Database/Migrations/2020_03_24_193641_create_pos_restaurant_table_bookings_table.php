<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePosRestaurantTableBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pos_restaurant_table_bookings', function (Blueprint $table) {
            $table->increments('id');

            $table->string('booking_id', 100)->nullable();
            $table->string('customer_name', 100)->nullable();
            $table->string('customer_email', 100)->nullable();
            $table->integer('booked_seat')->nullable();
            $table->date('booked_date')->nullable();
            $table->time('booked_time_from')->nullable();
            $table->time('booked_time_to')->nullable();
            $table->boolean('status')->default(0);

            $table->integer('customer_id')->unsigned()->nullable();

            $table->integer('table_id')->unsigned()->nullable();
            $table->foreign('table_id')->references('id')->on('pos_restaurant_tables')->onDelete('cascade');
            
            $table->integer('agent_id')->unsigned()->nullable();
            $table->foreign('agent_id')->references('id')->on('pos_users')->onDelete('cascade');

            $table->integer('order_id')->unsigned()->nullable();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            
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
        Schema::dropIfExists('pos_restaurant_table_bookings');
    }
}
