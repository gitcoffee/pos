<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePosRestaurantTablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pos_restaurant_tables', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name', 50)->nullable();
            $table->string('type', 50)->nullable();
            $table->integer('position')->nullable();
            $table->integer('no_of_seat')->nullable();
            $table->boolean('status')->default(0);
            
            $table->integer('agent_id')->unsigned()->nullable();
            $table->foreign('agent_id')->references('id')->on('pos_users')->onDelete('cascade');

            $table->unique(['position', 'agent_id'], 'pos_restaurant_tables_position_agent_id_unique_index');

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
        Schema::dropIfExists('pos_restaurant_tables');
    }
}
