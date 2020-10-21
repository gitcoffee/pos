<?php

namespace Webkul\Pos\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * RestaurantTableBooking JsonResource
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class RestaurantTableBooking extends JsonResource
{
    /**
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct($resource)
    {
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'booking_id'        => $this->booking_id,
            'agent_id'          => $this->agent_id,
            'agent_name'        => $this->agent_name,
            'table_name'        => $this->name,
            'table_type'        => $this->type,
            'customer_name'     => $this->customer_name,
            'customer_email'    => $this->customer_email,
            'booked_seat'       => $this->booked_seat,
            'booked_date'       => $this->booked_date,
            'booked_time_from'  => $this->booked_time_from,
            'booked_time_to'    => $this->booked_time_to,
            'status'            => $this->status,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
        ];
    }
}