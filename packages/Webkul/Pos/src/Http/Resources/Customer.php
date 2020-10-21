<?php

namespace Webkul\Pos\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Customer JsonResource
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class Customer extends JsonResource
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
        $default_address = [];
        $format_address = [];
        $customer = $this->customer ? $this->customer : $this;

        foreach ($customer->addresses as $address) {
            if ($address->default_address) {
                $default_address = $address;
            }
        }

        if ( isset($default_address->id) ) {
            array_push($format_address, $default_address->address1);
            array_push($format_address, $default_address->city);
            array_push($format_address, $default_address->state);
            array_push($format_address, $default_address->country);
        }

        return [
            'id'                => $this->id,
            'first_name'        => $this->first_name,
            'last_name'         => $this->last_name,
            'name'              => $this->first_name . ' '. $this->last_name,
            'email'             => $this->email,
            'gender'            => $this->gender,
            'date_of_birth'     => $this->date_of_birth,
            'customer_group_id' => $this->customer_group_id,
            'phone'             => ($this->phone ? $this->phone : (isset($default_address->phone) ? $default_address->phone : '')),
            'addresses'         => $default_address,
            'faddresses'        => implode(", ", $format_address),
            'status'            => $this->status,
            'subscribed_to_news_letter' => $this->subscribed_to_news_letter,
            'is_verified'       => $this->is_verified,
            'isActive'          => false,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
        ];
    }
}