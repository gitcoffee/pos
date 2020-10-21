<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBaseTenderedAmountAndBaseChangeAmountToPosCustomerCredit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pos_customer_credit', function($table) {
            $table->decimal('base_change_amount', 12,4)->after('change_amount');
            $table->decimal('base_tendered_amount', 12,4)->after('tendered_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pos_customer_credit', function($table) {
            $table->dropColumn('base_change_amount');
            $table->dropColumn('base_tendered_amount');
        });
    }
}
