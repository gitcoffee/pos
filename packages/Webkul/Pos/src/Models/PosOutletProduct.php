<?php

namespace Webkul\Pos\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Pos\Contracts\PosOutletProduct as PosOutletProductContract;

/**
 * PosOutletProduct Model
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosOutletProduct extends Model implements PosOutletProductContract
{
    protected $table = 'pos_outlet_product';

    protected $fillable = ['id', 'product_id', 'outlet_id', 'status'];

    public function outlets() {
        return $this->belongsToMany(PosOutletProxy::modelClass(), 'pos_outlet_product', 'product_id', 'id');
    }
}