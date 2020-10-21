<?php

namespace Webkul\Pos\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Pos\Contracts\PosBank as PosBankContract;

/**
 * PosBank Model
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosBank extends Model implements PosBankContract
{
    protected $table = 'pos_banks';

    protected $fillable = ['id', 'agent_id', 'name', 'address', 'email', 'phone', 'status'];


    public function users() {
        return $this->belongsToMany(PosUserProxy::modelClass(), 'pos_banks', 'agent_id', 'id');
    }
}