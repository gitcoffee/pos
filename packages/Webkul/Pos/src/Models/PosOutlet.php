<?php

namespace Webkul\Pos\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Pos\Contracts\PosOutlet as PosOutletContract;

/**
 * PosOutlet Model
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosOutlet extends Model implements PosOutletContract
{
    protected $table = 'pos_outlets';

    protected $fillable = ['id', 'name', 'address', 'country', 'state', 'city', 'postcode', 'status', 'inventory_source_id'];

    //for joining the two way pivot table
    public function outlet_products() {
        return $this->belongsToMany(PosOutletProductProxy::modelClass(), 'pos_outlet_product', 'outlet_id')->withPivot('id');
    }
    
    public function products() {
        return $this->hasMany(PosOutletProductProxy::modelClass(), 'outlet_id');
    }
}