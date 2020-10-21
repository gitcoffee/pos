<?php

namespace Webkul\Pos\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Pos\Contracts\PosProductRequest as PosProductRequestContract;

/**
 * PosProductRequest Model
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosProductRequest extends Model implements PosProductRequestContract
{
    protected $table = 'pos_product_request';

    protected $fillable = ['id', 'user_id', 'product_id', 'options', 'requested_quantity', 'supplier_id', 'comment', 'send_status', 'request_status'];

    public function users() {
        return $this->belongsToMany(PosUserProxy::modelClass(), 'pos_product_request', 'user_id', 'id');
    }
}