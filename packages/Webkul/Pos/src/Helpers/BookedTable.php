<?php

namespace Webkul\Pos\Helpers;

use Carbon\Carbon;
use DB;
use Webkul\Pos\Repositories\PosTableBookingRepository;

class BookedTable
{
    /**
     * PosTableBookingRepository object
     */
    protected $posTableBookingRepository;

    /**
     * Create a new helper instance.
     *
     * @param  Webkul\Pos\Repositories\PosTableBookingRepository              $posTableBookingRepository
     * @return void
     */
    public function __construct(PosTableBookingRepository $posTableBookingRepository)
    {
        $this->posTableBookingRepository = $posTableBookingRepository;
    }

    /**
     * Returns the allowed variants
     *
     * @param Product $product
     * @return float
     */
    public function getTableRemainingSeats($data)
    {
        $current_date_time = Carbon::now()->format('Y-m-d') . ' ' . Carbon::now()->format('H:i:s');

        $dbQuery = $this->posTableBookingRepository->scopeQuery(function ($query) use ($current_date_time, $data) {
            return $query->where('pos_restaurant_table_bookings.agent_id', '=', $data['agent_id'])->where('status', '=', 1)->where('pos_restaurant_table_bookings.table_id', '=', $data['table_id'])->where(DB::raw("CONCAT(pos_restaurant_table_bookings.booked_date, ' ', pos_restaurant_table_bookings.booked_time_to)"), '>=', $current_date_time);
        });

        return $dbQuery->sum('pos_restaurant_table_bookings.booked_seat');
    }
}