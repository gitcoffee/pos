<?php

namespace Webkul\Pos\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Pos\Contracts\PosTableBooking as PosTableBookingContract;

/**
 * PosTableBooking Model
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosTableBooking extends Model implements PosTableBookingContract
{
    protected $table = 'pos_restaurant_table_bookings';

    protected $fillable = [
        'id',
        'booking_id',
        'customer_id',
        'customer_name',
        'customer_email',
        'booked_seat',
        'booked_date',
        'booked_time_from',
        'booked_time_to',
        'status',
        'table_id',
        'agent_id',
    ];

    public function table() {
        return $this->belongsTo(PosTableProxy::modelClass());
    }

    public function agent() {
        return $this->belongsTo(PosUserProxy::modelClass());
    }

    public function customer() {
        return $this->belongsTo(PosUserProxy::modelClass());
    }
}