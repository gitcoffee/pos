<?php

namespace Webkul\Pos\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Pos\Contracts\PosCustomerCredit as PosCustomerCreditContract;

/**
 * PosCustomerCredit Model
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosCustomerCredit extends Model implements PosCustomerCreditContract
{
    protected $table = 'pos_customer_credit';

    protected $fillable = ['id', 'order_id', 'customer_id', 'change_amount', 'base_change_amount', 'tendered_amount', 'base_tendered_amount', 'payment_mode', 'used_status'];
}