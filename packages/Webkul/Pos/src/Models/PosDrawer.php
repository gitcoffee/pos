<?php

namespace Webkul\Pos\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Pos\Contracts\PosDrawer as PosDrawerContract;

/**
 * PosDrawer Model
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosDrawer extends Model implements PosDrawerContract
{
    protected $table = 'pos_user_drawer';

    protected $fillable = ['id', 'user_id', 'outlet_id', 'base_currency', 'opening_currency', 'opening_amount', 'status', 'remark' ];

}