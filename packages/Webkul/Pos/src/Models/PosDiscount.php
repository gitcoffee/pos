<?php

namespace Webkul\Pos\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Pos\Contracts\PosDiscount as PosDiscountContract;

/**
 * PosDiscount Model
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosDiscount extends Model implements PosDiscountContract
{
    protected $table = 'pos_discount';

    protected $fillable = ['id', 'offername', 'user_id', 'fromprice', 'toprice', 'type', 'value', 'status'];

    public function users() {
        return $this->belongsToMany(PosUserProxy::modelClass(), 'pos_discount', 'user_id', 'id');
    }
}