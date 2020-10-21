<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPosOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pos_order', function($table) {
            $table->string('bank_name', 50)->nullable();
            $table->integer('card_detail')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pos_order', function($table) {
            $table->dropColumn('bank_name');
            $table->dropColumn('card_detail');
        });
    }
}
