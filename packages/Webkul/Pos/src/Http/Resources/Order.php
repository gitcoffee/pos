<?php

namespace Webkul\Pos\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\API\Http\Resources\Core\Channel as ChannelResource;
use Webkul\API\Http\Resources\Customer\Customer as CustomerResource;
use Webkul\API\Http\Resources\Sales\Invoice;
use Webkul\API\Http\Resources\Sales\Shipment;
use Webkul\Pos\Repositories\PosCustomerCreditRepository;

/**
 * Order JsonResource
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class Order extends JsonResource
{
    /**
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct($resource)
    {
        $this->posCustomerCredit = app(PosCustomerCreditRepository::class);

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
        $posCustomerCredit = $this->posCustomerCredit->findByField('order_id', $this->id)->first();

        $payment_mode = '';
        if ( isset($posCustomerCredit['order_id']) && $posCustomerCredit['order_id']) {
            $payment_mode = $posCustomerCredit['payment_mode'];
        }
        
        $order_barcode = '';
        if ( $this->order_barcode_path ) {
            $order_barcode = asset('/storage' . $this->order_barcode_path);
        }
        
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'order_note' => $this->order_note,
            'status' => $this->status,
            'channel_name' => $this->channel_name,
            'is_guest' => $this->is_guest,
            'customer_email' => $this->customer_email,
            'customer_phone' => $this->customer_phone,
            'customer_first_name' => $this->customer_first_name,
            'customer_last_name' => $this->customer_last_name,
            'shipping_method' => $this->shipping_method,
            'shipping_title' => $this->shipping_title,
            'shipping_description' => $this->shipping_description,
            'coupon_code' => $this->coupon_code,
            'is_gift' => $this->is_gift,
            'total_item_count' => $this->total_item_count,
            'total_qty_ordered' => $this->total_qty_ordered,
            
            'base_currency_code' => $this->base_currency_code,
            'channel_currency_code' => $this->channel_currency_code,
            'order_currency_code' => $this->order_currency_code,
            
            'grand_total' => $this->grand_total,
            'base_grand_total' => $this->base_grand_total,
            'grand_total_invoiced' => $this->grand_total_invoiced,
            'base_grand_total_invoiced' => $this->base_grand_total_invoiced,
            'grand_total_refunded' => $this->grand_total_refunded,
            'base_grand_total_refunded' => $this->base_grand_total_refunded,
            'sub_total' => $this->sub_total,
            'base_sub_total' => $this->base_sub_total,
            'sub_total_invoiced' => $this->sub_total_invoiced,
            'base_sub_total_invoiced' => $this->base_sub_total_invoiced,
            'sub_total_refunded' => $this->sub_total_refunded,
            'discount_percent' => $this->discount_percent,
            'discount_amount' => $this->discount_amount,
            'base_discount_amount' => $this->base_discount_amount,
            'discount_invoiced' => $this->discount_invoiced,
            'base_discount_invoiced' => $this->base_discount_invoiced,
            'discount_refunded' => $this->discount_refunded,
            'base_discount_refunded' => $this->base_discount_refunded,
            'tax_amount' => $this->tax_amount,
            'base_tax_amount' => $this->base_tax_amount,
            'tax_amount_invoiced' => $this->tax_amount_invoiced,
            'base_tax_amount_invoiced' => $this->base_tax_amount_invoiced,
            'tax_amount_refunded' => $this->tax_amount_refunded,
            'base_tax_amount_refunded' => $this->base_tax_amount_refunded,
            'shipping_amount' => $this->shipping_amount,
            'base_shipping_amount' => $this->base_shipping_amount,
            'shipping_invoiced' => $this->shipping_invoiced,
            'base_shipping_invoiced' => $this->base_shipping_invoiced,
            'shipping_refunded' => $this->shipping_refunded,
            'base_shipping_refunded' => $this->base_shipping_refunded,
            'tendered_amount' => $posCustomerCredit['tendered_amount'],
            'base_tendered_amount' => $posCustomerCredit['base_tendered_amount'],
            'change_amount' => $posCustomerCredit['change_amount'],
            'base_change_amount' => $posCustomerCredit['base_change_amount'],
            'total_amount' => $this->total_amount,
            'base_total_amount' => $this->base_total_amount,
            'total_amount' => $this->total_amount,
            'base_total_amount' => $this->base_total_amount,
            
            'base_currency_symbol' => core()->currencySymbol($this->base_currency_code),
            'order_currency_symbol' => core()->currencySymbol($this->order_currency_code),
            
            'payment_mode' => ucfirst($payment_mode),
            'pos_entry_id' => $this->pos_entry_id,
            'pos_order_id' => $this->pos_order_id,
            'bank_name' => $this->bank_name,
            'order_note' => $this->pos_order_note,
            'ref_id' => $this->order_ref_id,
            'order_barcode' => $order_barcode,
            'customer' => $this->when($this->customer_id, new CustomerResource($this->customer)),
            'channel' => $this->when($this->channel_id, new ChannelResource($this->channel)),
            'updated_at' => $this->updated_at,
            'items' => OrderItem::collection($this->items),
            
            'invoices' => Invoice::collection($this->invoices),
            'shipments' => Shipment::collection($this->shipments),
            'created_at' => $this->created_at->format('F d, Y H:i:s'),
            
            //booking table information
            'booking_id'        => $this->booking_id,
            'booked_seat'       => $this->booked_seat,
            'booked_date'       => $this->booked_date,
            'booked_time_from'  => $this->booked_time_from,
            'booked_time_to'    => $this->booked_time_to,
            'table_name'        => $this->table_name,
            'table_type'        => $this->table_type,
        ];
    }
}