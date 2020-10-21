<?php

namespace Webkul\Pos\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * RestaurantTable JsonResource
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class RestaurantTable extends JsonResource
{
    /**
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct($resource)
    {
        parent::__construct($resource);

        $this->bookedTable = app('Webkul\Pos\Helpers\BookedTable');
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $booked_seat = $this->bookedTable->getTableRemainingSeats([
            'agent_id'  => $this->agent_id,
            'table_id'  => $this->id,
            ]);
            
        return [
            'id'            => $this->id,
            'agent_id'      => $this->agent_id,
            'agent_name'    => $this->agent_name,
            'name'          => $this->name,
            'type'          => $this->type,
            'position'      => $this->position,
            'no_of_seat'    => $this->no_of_seat,
            'remaining_seats' => ($this->no_of_seat - $booked_seat),
            'status'        => $this->status,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}