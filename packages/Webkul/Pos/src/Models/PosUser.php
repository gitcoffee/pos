<?php

namespace Webkul\Pos\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Webkul\Pos\Contracts\PosUser as PosUserContract;

/**
 * PosProductRequest Authenticatable Model
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosUser extends Authenticatable implements PosUserContract
{
    protected $table = 'pos_users';

    protected $fillable = ['id', 'outlet_id', 'username', 'password', 'firstname', 'lastname', 'email', 'image', 'low_stock', 'report_type', 'status', 'changed' ];

    protected $hidden = ['password', 'remember_token'];

    /**
     * Get the Pos Agent full name.
     */
    public function getNameAttribute() {
        return ucfirst($this->firstname) . ' ' . ucfirst($this->lastname);
    }

    //for joining the two way pivot table
    public function outlet_products() {
        return $this->belongsToMany(PosDiscountProxy::modelClass(), 'pos_discount', 'user_id')->withPivot('id');
    }
    
    public function discounts() {
        return $this->hasMany(PosDiscountProxy::modelClass(), 'user_id');
    }

    public function banks() {
        return $this->hasMany(PosBankProxy::modelClass(), 'agent_id');
    }

    public function tables() {
        return $this->hasMany(PosTableProxy::modelClass(), 'agent_id');
    }

    public function tableBookings() {
        return $this->hasMany(PosTableBookingProxy::modelClass(), 'agent_id');
    }
}