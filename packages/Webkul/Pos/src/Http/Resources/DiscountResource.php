<?php

namespace Webkul\Pos\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Discount JsonResource
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class Discount extends JsonResource
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
        if ( $this->type == 'percentage') {
            $discount_amount = $this->value . '%';
        } else {
            $discount_amount = core()->currency($this->value);
        }
        
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'offername' => $this->offername,
            'fromprice' => $this->fromprice,
            'converted_fromprice' => number_format(core()->convertPrice($this->fromprice, core()->getCurrentCurrencyCode()), 2, '.', ''),
            'toprice' => $this->toprice,
            'converted_toprice' => number_format(core()->convertPrice($this->toprice, core()->getCurrentCurrencyCode()), 2, '.', ''),
            'ffromprice' => core()->currency($this->fromprice),
            'ftoprice' => core()->currency($this->toprice),
            'type' => $this->type,
            'fvalue' => $discount_amount,
            'value' => $this->value,
            'converted_value' => number_format(core()->convertPrice($this->value, core()->getCurrentCurrencyCode()), 2, '.', ''),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}