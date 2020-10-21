<?php

namespace Webkul\Pos\Repositories;

use Webkul\Core\Eloquent\Repository;
use DB;

/**
 * PosTableBooking Repository
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosTableBookingRepository extends Repository
{

    public function model() {
        return 'Webkul\Pos\Contracts\PosTableBooking';
    }

    /**
     * @return Collection
     */
    public function getAll()
    {
        $params = request()->input();
        
        $results = app('Webkul\Pos\Repositories\PosTableBookingRepository')->scopeQuery(function($query) use($params) {

            $qb = $query->distinct()
                    ->addSelect('pos_restaurant_table_bookings.*')
                    ->addSelect('pos_restaurant_tables.name', 'pos_restaurant_tables.type')
                    ->addSelect(DB::raw('CONCAT(pos_users.firstname, " ", pos_users.lastname) as agent_name'))
                    ->leftJoin('pos_restaurant_tables', 'pos_restaurant_table_bookings.table_id', '=', 'pos_restaurant_tables.id')
                    ->leftJoin('pos_users', 'pos_restaurant_table_bookings.agent_id', '=', 'pos_users.id')
                    ->where('pos_restaurant_tables.status', 1);

                    if (isset($params['booking_status'])) {
                        $qb->where('pos_restaurant_table_bookings.status', $params['booking_status']);
                    }

                    if (isset($params['order_id'])) {
                        $qb->where('pos_restaurant_table_bookings.order_id', $params['order_id']);
                    }

                    if (isset($params['user_id']) && $params['user_id']) {
                        $qb->where('pos_restaurant_table_bookings.agent_id', $params['user_id']);
                    }

                    if (isset($params['booking_id']) && $params['booking_id']) {
                        $qb->where('pos_restaurant_table_bookings.booking_id', $params['booking_id']);
                    }
                    
                    if (isset($params['filter_name']) && $params['filter_name']) {
                        $booking_filter = $params['filter_name'];
                        $booking = explode("#", $params['filter_name']);

                        if ( count($booking) > 1 && isset($booking[1]) ) {
                            $booking_filter = $booking[1];
                        } else if (isset($booking[0])) {
                            $booking_filter = $booking[0];
                        }

                        $qb->where('pos_restaurant_tables.name', 'LIKE', '%' . urldecode($booking_filter) .'%')
                            ->orWhere('pos_restaurant_table_bookings.booking_id', 'LIKE', '%' . urldecode($booking_filter) .'%')
                            ->orWhere('pos_restaurant_table_bookings.customer_name', 'LIKE', '%' . urldecode($booking_filter) .'%')
                            ->orWhere('pos_restaurant_table_bookings.customer_email', 'LIKE', '%' . urldecode($booking_filter) .'%')->orWhere('pos_restaurant_table_bookings.booked_date', 'LIKE', '%' . urldecode($booking_filter) .'%')
                            ->orWhere(DB::raw("CONCAT(pos_restaurant_table_bookings.booked_date, ' ', pos_restaurant_table_bookings.booked_time_from)"), 'LIKE', '%' . urldecode($booking_filter) .'%')
                            ->orWhere(DB::raw("CONCAT(pos_restaurant_table_bookings.booked_date, ' ', pos_restaurant_table_bookings.booked_time_to)"), 'LIKE', '%' . urldecode($booking_filter) .'%')
                            ->orWhere(DB::raw("CONCAT(pos_restaurant_table_bookings.booked_time_from, ' - ', pos_restaurant_table_bookings.booked_time_to)"), 'LIKE', '%' . urldecode($booking_filter) .'%');
                    }

                    return $qb->orderBy('pos_restaurant_table_bookings.booked_date')->groupBy('pos_restaurant_table_bookings.id');
        })->paginate(isset($params['limit']) ? $params['limit'] : 10);
        
        return $results;
    }
}