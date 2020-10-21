<?php

namespace Webkul\Pos\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Pos\Contracts\PosOrder as PosOrderContract;

/**
 * PosOrder Model
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosOrder extends Model implements PosOrderContract
{
    protected $table = 'pos_order';

    protected $fillable = ['id', 'order_id', 'outlet_id', 'user_id', 'order_ref_id', 'order_note', 'discount_amount', 'base_discount_amount', 'order_currency', 'order_barcode_path', 'bank_name', 'card_detail'];

}