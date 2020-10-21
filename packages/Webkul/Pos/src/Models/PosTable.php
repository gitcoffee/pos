<?php

namespace Webkul\Pos\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Pos\Contracts\PosTable as PosTableContract;

/**
 * PosTable Model
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosTable extends Model implements PosTableContract
{
    protected $table = 'pos_restaurant_tables';

    protected $fillable = ['id', 'agent_id', 'name', 'type', 'position', 'no_of_seat', 'status'];


    public function users() {
        return $this->belongsToMany(PosUserProxy::modelClass(), 'pos_restaurant_tables', 'agent_id', 'id');
    }
}