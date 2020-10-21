<?php

namespace Webkul\Pos\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Webkul\Pos\Repositories\PosOutletRepository as PosOutlet;
use Webkul\Pos\Repositories\PosOrderRepository as PosOrder;
use Webkul\Pos\Repositories\PosDrawerRepository as PosDrawer;
use Webkul\Pos\Repositories\PosDiscountRepository as PosDiscount;
use Webkul\Product\Repositories\ProductRepository as Product;
use Webkul\Attribute\Repositories\AttributeFamilyRepository as AttributeFamily;
use Webkul\Pos\Repositories\PosOutletProductRepository as PosOutletProduct;
use Webkul\Pos\Repositories\PosCustomerCreditRepository as PosCustomerCredit;
use Webkul\Sales\Repositories\OrderRepository as Order;
use Webkul\Pos\Repositories\ShipmentRepository as Shipment;
use Webkul\Pos\Repositories\InvoiceRepository as Invoice;
use Webkul\Customer\Repositories\CustomerRepository as Customer;
use Webkul\Pos\Http\Resources\Order as OrderResource;
use Webkul\Pos\Helpers\Price;
use Webkul\Tax\Repositories\TaxCategoryRepository as TaxCategory;
use Webkul\Pos\Repositories\PosTableBookingRepository;

/**
 * PosOrder controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com>
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $_config;

    /**
     * product object
     *
     * @var array
     */
    protected $product;
    
    /**
     * AttributeFamily object
     *
     * @var array
     */
    protected $attributeFamily;

    
    /**
     * customer object
     *
     * @var array
     */
    protected $customer;

    /**
     * order object
     *
     * @var array
     */
    protected $order;

    /**
     * shipment object
     *
     * @var array
     */
    protected $shipment;

    /**
     * shipment object
     *
     * @var array
     */
    protected $invoice;
    

    /**
     * posOutletProduct object
     *
     * @var array
     */
    protected $posOutletProduct;

    /**
     * posOutlet object
     *
     * @var array
     */
    protected $posOutlet;

    /**
     * posOrder object
     *
     * @var array
     */
    protected $posOrder;

    /**
     * posOrder object
     *
     * @var array
     */
    protected $posDrawer;
    
    /**
     * posDiscount object
     *
     * @var array
     */
    protected $posDiscount;

    /**
     * posOrder object
     *
     * @var array
     */
    protected $posCustomerCredit;

    /**
     * Product price helper instance
    */
    protected $price;

    protected $taxCategory;

    /**
     * PosTableBookingRepository object
     *
     * @var array
     */
    protected $posTableBookingRepository;
    

    public function __construct(
        Product $product,
        AttributeFamily $attributeFamily,
        Customer $customer,
        Order $order,
        Shipment $shipment,
        Invoice $invoice,
        PosOutletProduct $posOutletProduct,
        PosOutlet $posOutlet,
        PosOrder $posOrder,
        PosDrawer $posDrawer,
        PosDiscount $posDiscount,
        PosCustomerCredit $posCustomerCredit,
        Price $price,
        TaxCategory $taxCategory,
        PosTableBookingRepository $posTableBookingRepository
    )   {
        $this->middleware('posuser');
        
        $this->_config = request('_config');
        
        $this->product = $product;

        $this->attributeFamily = $attributeFamily;

        $this->customer = $customer;

        $this->order = $order;

        $this->shipment = $shipment;

        $this->invoice = $invoice;
        
        $this->posOutletProduct = $posOutletProduct;
        
        $this->posOutlet = $posOutlet;

        $this->posOrder = $posOrder;

        $this->posDrawer = $posDrawer;

        $this->posDiscount = $posDiscount;
        
        $this->posCustomerCredit = $posCustomerCredit;

        $this->price = $price;

        $this->taxCategory = $taxCategory;

        $this->posTableBookingRepository = $posTableBookingRepository;   
    }

    public function saveOrder() {
        $data = [];
        $pos_data = [];
        $customer = [];
        $params = request()->all();

        if ( auth()->guard('posuser')->check() && isset($params['order_data']) ) {
            $user_id = auth()->guard('posuser')->user()->id;

            $data = $params['order_data'];

            if ( isset($data['pos_order']['user_id']) && $user_id == $data['pos_order']['user_id']) {
                $pos_data = $data['pos_order'];
                unset($data['pos_order']);

                $posOutlet = $this->posOutlet->find($pos_data['outlet_id']);

                // Customer manage
                if ( isset($data['customer']) && isset($data['customer']['id']) ) {
                    $customer = $this->customer->findOneByField('email', $data['customer']['email']);
                    if (! $data['customer']['id'] && !isset($customer->id)) {
                        unset($data['customer']['id']);
                        
                        $data['password'] = bcrypt($data['email']);
                        $customer = $this->customer->create($data['customer']);
                    }
                    $customer_address = $customer->addresses->first();
                }

                $for_shipping_count = 0;
                
                $order_data = [
                    'cart_id'               => null,
                    'is_guest'              => 0,
                    'customer_email'        => $customer->email,
                    'customer_first_name'   => $customer->first_name,
                    'customer_last_name'    => $customer->last_name,
                    'customer'              => $customer,
                    'channel'               => core()->getDefaultChannel(),
                    'shipping_method'       => 'free_free',
                    'shipping_title'        => 'Free Shipping - Free Shipping',
                    'shipping_description'  => 'This is a free shipping POS Order',
                    'shipping_amount'       => 0,
                    'base_shipping_amount'  => 0,
                    'payment'               => [
                        'method'        => ($data['payment_mode'] == 'cash') ? 'pos_cash' : 'pos_card',
                        'method_title'  => ($data['payment_mode'] == 'cash') ? 'Cash Payment' : 'Card Payment'
                    ],
                    'shipping_address'      => [
                        'first_name'            => $customer->first_name,
                        'last_name'             => $customer->last_name,
                        'email'                 => $customer->email,
                        'address1'              => $posOutlet->address,
                        'address2'              => '',
                        'country'               => $posOutlet->country,
                        'state'                 => $posOutlet->state,
                        'city'                  => $posOutlet->city,
                        'postcode'              => $posOutlet->postcode,
                        'phone'                 => $customer->phone ? $customer->phone : '123456789',
                        'address_type'          => 'cart_shipping'
                    ],
                    'billing_address'       => [
                        'first_name'            => $customer->first_name,
                        'last_name'             => $customer->last_name,
                        'email'                 => $customer->email,
                        'address1'              => isset($customer_address->address) ? $customer_address->address : $posOutlet->address,
                        'address2'              => '',
                        'country'               => isset($customer_address->country) ? $customer_address->country : $posOutlet->country,
                        'state'                 => isset($customer_address->state) ? $customer_address->state : $posOutlet->state,
                        'city'                  => isset($customer_address->city) ? $customer_address->city : $posOutlet->city,
                        'postcode'              => isset($customer_address->postcode) ? $customer_address->postcode : $posOutlet->postcode,
                        'phone' => $customer->phone ? $customer->phone : '123456789',
                        'address_type'          => 'cart_billing'
                    ],
                    'total_item_count'          => 0,
                    'total_qty_ordered'         => 0,
                    'base_currency_code'        => core()->getBaseCurrencyCode(),
                    'channel_currency_code'     => core()->getCurrentCurrencyCode(),
                    'order_currency_code'       => core()->getCurrentCurrencyCode(),
                    'tax_amount'                => 0,
                    'base_tax_amount'           => 0,
                    'discount_amount'           => 0,
                    'base_discount_amount'      => 0,
                    'sub_total'                 => 0,
                    'base_sub_total'            => 0,
                    'grand_total'               => 0,
                    'base_grand_total'          => 0,
                ];

                if ( isset($data['ref_id']) && $data['ref_id']) {
                    $pos_data['order_ref_id'] = $data['ref_id'];
                    unset($data['ref_id']);
                }
                
                foreach($data['order_items'] as $key => $orderItem) {
                    $children   = $additional = [];
                    $weight     = 0;
                    $order_data['total_item_count']     += 1;
                    $order_data['total_qty_ordered']    += $orderItem['qty_ordered'];

                    if ( $orderItem['id'] ) {
                        if ($orderItem['type'] == 'virtual' || $orderItem['type'] == 'downloadable' || $orderItem['type'] == 'booking') {
                            $for_shipping_count += 1;
                        }
                        
                        if ($orderItem['type'] != 'simple' && $orderItem['type'] != 'virtual') {
                            $additional = $orderItem['additional'];
                            $product    = $this->product->findOneByField('id', $additional['product_id']);

                            $weight      = 0;
                            if ( $orderItem['type'] == 'configurable' ) {
                                if ( isset($additional['super_attribute']) ) {
                                    $additional['super_attribute'] = array_filter($additional['super_attribute']);
                                }

                                $child   = $this->product->findOneByField('id', $orderItem['additional']['selected_configurable_option']);

                                if (! $child->haveSufficientQuantity($orderItem['qty_ordered']) ) {
                                    return response()->json([
                                        'status' => false,
                                        'message' => 'Warning: ' . $child->name . 'does not have sufficient quantity.'
                                    ]);
                                }
                                $price      = $child->getTypeInstance()->getMinimalPrice();
                                $weight     = floatval($child->weight);
                                $children[] = $child;
                            } else {
                                if ( $orderItem['type'] == 'bundle' ) {
                                    $getChildren = $product->getTypeInstance()->getCartChildProducts($additional);
                                    foreach ($getChildren as $key => $child) {
                                        $childProduct   = $this->product->findOrFail($child['product_id']);
                                        
                                        if (! bagisto_pos()->haveSufficientBundleQuantity($orderItem['additional'], $child) ) {
                                            return response()->json([
                                                'status'    => false,
                                                'message'   => 'Warning: ' . $childProduct->name . 'does not have sufficient quantity.'
                                            ]);
                                        }
                                        $weight     += floatval($childProduct->weight);
                                        $children[] = $childProduct;
                                    }
                                    
                                    $price  = bagisto_pos()->getBundleProductPrice($orderItem['additional']);
                                } else if ( $orderItem['type'] == 'downloadable' ) {
                                    $price  = bagisto_pos()->getDownloadProductPrice($orderItem['additional']);
                                    $download_attributes    = $orderItem['additional']['attributes'];
                                    
                                    if ( isset($download_attributes[$product->id]) && isset($download_attributes[$product->id]['download_links'])) {
                                        $download_links         = $download_attributes[$product->id]['download_links'];
                                        
                                        $price = $product->price;

                                        $option_price = 0;
                                        foreach( $download_links as $link) {
                                            $option_price   = $option_price + $link['price'];
                                        }

                                        $price = bagisto_pos()->getCustomerGroupPrice($product, $orderItem['qty_ordered'], $customer);
                                        $price = $price + $option_price; 
                                    }
                                } else if ( $orderItem['type'] == 'booking' ) {
                                    $price  = bagisto_pos()->getBookingProductPrice($orderItem['additional']);
                                } else {
                                    $price  =  $product->price;
                                }
                            }
                        } else {
                            $product = $this->product->findOneByField('id', $orderItem['id']);
                
                            if (! $product->haveSufficientQuantity($orderItem['qty_ordered']) ) {
                                return response()->json([
                                    'status' => false,
                                    'message' => 'Warning: ' . $product->name . 'does not have sufficient quantity.'
                                ]);
                            }

                            $price  = $product->getTypeInstance()->getMinimalPrice();

                            $price = bagisto_pos()->getCustomerGroupPrice($product, $orderItem['qty_ordered'], $customer);
                            
                            $weight = floatval($product->weight);
                        }

                        $tax_percent = $this->getTaxPercentage($product->tax_category_id, $posOutlet);

                        $product_tax_amount = (core()->convertPrice($price * $orderItem['qty_ordered']) * $tax_percent) / 100;
                        $order_data['tax_amount'] += $product_tax_amount;

                        $product_base_tax_amount = (($price * $orderItem['qty_ordered']) * $tax_percent) / 100;
                        $order_data['base_tax_amount'] += $product_base_tax_amount;

                        $order_data['items'][$key] = [
                            'id'            => $product->id,
                            'sku'           => $product->sku,
                            'name'          => $product->name,
                            'type'          => $product->type,
                            'qty_ordered'   => $orderItem['qty_ordered'],
                            'weight'        => $weight,
                            'total_weight'  => $weight * $orderItem['qty_ordered'],
                            'price'         => core()->convertPrice($price),
                            'base_price'    => $price,
                            'total'         => core()->convertPrice($price * $orderItem['qty_ordered']),
                            'base_total'    => $price * $orderItem['qty_ordered'],
                            'tax_percent'   => $tax_percent,
                            'tax_amount'    => $product_tax_amount,
                            'base_tax_amount' => $product_base_tax_amount,
                            'product'       => $product,
                            'additional'    => $additional,
                        ];

                        if (!empty($children)) {
                            foreach ($children as $child) {
                                $order_data['items'][$key]['children'][] = [
                                    'product'           => $child,
                                    'sku'               => $child->sku,
                                    'type'              => $child->type,
                                    'name'              => $child->name,
                                    'weight'            => $child->weight,
                                    'total_weight'      => 0.00,
                                    'qty_ordered'       => 0,
                                    'price'             => 1.00,
                                    'base_price'        => 0.00,
                                    'total'             => 0.00,
                                    'base_total'        => 0.00,
                                    'tax_percent'       => 0.00,
                                    'tax_amount'        => 0.00,
                                    'base_tax_amount'   => 0.00,
                                    'additional'        => ''
                                ];
                            }
                        }

                        $order_data['sub_total'] += core()->convertPrice($price * $orderItem['qty_ordered']);
                        $order_data['base_sub_total'] += $price * $orderItem['qty_ordered'];

                    } else {
                        // In case of custom product ordered
                        if ( !$orderItem['sku'] ) {
                            $sku = 'custom-' . time() . '-' . ($key + 1);
                            $data['order_items'][$key]['sku'] = $sku;
                        } else {
                            $sku = $orderItem['sku'];
                            $data['order_items'][$key]['sku'] = $sku;
                        }
                        
                        $createProduct = [
                            'type'                  => $orderItem['type'],
                            'attribute_family_id'   => $this->attributeFamily->first()->id,'sku' => $sku,
                        ];

                        if ($productId = $this->product->create($createProduct)->id) {
                            $inventories = [];
                            $inventories[$posOutlet->inventory_source_id] = $orderItem['qty_ordered'];
                            
                            $updateProduct = [
                                'channel'       => core()->getDefaultChannel()->code,
                                'locale'        => app()->getLocale(),
                                'sku'           => $sku,
                                'name'          => $orderItem['name'],
                                'url_key'       => 'custom-' . time() . '-' . ($key + 1),
                                'new'           => 0,
                                'featured'      => 0,
                                'visible_individually' => 0,
                                'status'        => 1,
                                'price'         => core()->convertToBasePrice($orderItem['price']),
                                'weight'        => $orderItem['weight'],
                                'inventories'   => $inventories,
                            ];

                            $this->product->update($updateProduct, $productId);

                            $product = $this->product->find($productId);

                            $order_data['items'][$key] = [
                                'id'                => $product->id,
                                'sku'               => $product->sku,
                                'name'              => $product->name,
                                'type'              => $product->type,
                                'qty_ordered'       => $orderItem['qty_ordered'],
                                'weight'            => $orderItem['weight'],
                                'total_weight'      => $orderItem['weight'] * $orderItem['qty_ordered'],
                                'price'             => $orderItem['price'],
                                'base_price'        => core()->convertToBasePrice($orderItem['price']),
                                'total'             => $orderItem['price'] * $orderItem['qty_ordered'],
                                'base_total'        => core()->convertToBasePrice($orderItem['price'] * $orderItem['qty_ordered']),
                                'tax_percent'       => 0,
                                'tax_amount'        => 0,
                                'base_tax_amount'   => 0,
                                'product'           => $product,
                                'additional'        => $additional,
                            ];

                            $order_data['sub_total'] += ($orderItem['price'] * $orderItem['qty_ordered']);
                            
                            $order_data['base_sub_total'] += core()->convertToBasePrice($orderItem['price']) * $orderItem['qty_ordered'];
                        } else {
                            continue;
                        }
                    }
                }

                if ( count($data['order_items']) == $for_shipping_count) {
                    $order_data['shipping_method']      = '';
                    $order_data['shipping_title']       = '';
                    $order_data['shipping_description'] = '';
                    $order_data['shipping_address']     = [];
                }

                if ( isset($data['discount_id']) && $data['discount_id']) {
                    $posDiscount = $this->posDiscount->findOneWhere(['status' => 1, 'id' => $data['discount_id']]);

                    if ( isset($posDiscount->id) ) {
                        $from_price = core()->convertPrice($posDiscount->fromprice);
                        $to_price = core()->convertPrice($posDiscount->toprice);

                        if ( ($from_price <= $order_data['sub_total']) && ($to_price >= $order_data['sub_total']) ) {
                            if ( $posDiscount->type == 'percentage' ) {
                                $order_data['discount_amount'] = (($order_data['sub_total'] * $posDiscount->value) / 100);
                                $order_data['base_discount_amount'] = (($order_data['base_sub_total'] * $posDiscount->value) / 100);
                            } else {
                                $order_data['discount_amount'] = core()->convertPrice($posDiscount->value);
                                $order_data['base_discount_amount'] = $posDiscount->value;
                            }
                        }
                    }
                }

                $order_data['grand_total']      = (($order_data['sub_total'] + $order_data['tax_amount']) - $order_data['discount_amount']);
                $order_data['base_grand_total'] = (($order_data['base_sub_total'] + $order_data['base_tax_amount']) - $order_data['base_discount_amount']);

                $order = $this->order->create($order_data);
                
                if (isset($order->id) && $order->id && !empty($pos_data) ) {
                    $pos_data['order_id']               = $order->id;
                    $pos_data['discount_amount']        = $order_data['discount_amount'];
                    $pos_data['base_discount_amount']   = $order_data['base_discount_amount'];

                    if (core()->getConfigData('pos.configuration.bill-receipt.show_barcode')) {
                        $pos_data['order_barcode_path'] = $this->posOrder->generateOrderBarcode($order->id);
                    }

                    // Add Customer Credit Entry
                    $customer_credit_data = [
                        'order_id'              => $order->id,
                        'customer_id'           => $customer->id,
                        'tendered_amount'       => $data['tendered_amount'],
                        'base_tendered_amount'  => core()->convertToBasePrice($data['tendered_amount'], core()->getCurrentCurrencyCode()),
                        'change_amount'         => $data['tendered_amount'] - $order_data['grand_total'],
                        'base_change_amount'    => core()->convertToBasePrice(($data['tendered_amount'] - $order_data['grand_total']), core()->getCurrentCurrencyCode()),
                        'payment_mode'          => $data['payment_mode'],
                        'used_status'           => false,
                    ];
                    $this->posCustomerCredit->create($customer_credit_data);

                    if ( isset($pos_data['booking_id']) && $pos_data['booking_id']) {
                        $booking_record = $this->posTableBookingRepository->findOneWhere([
                            'booking_id'    => $pos_data['booking_id'],
                            'agent_id'      => $pos_data['user_id'],
                        ]);

                        if ( isset($booking_record->id) ) {
                            $booking_record->order_id = $order->id;
                            $booking_record->status = 0;
                            $booking_record->save();
                        }
                    }

                    // Add POS Order Entry
                    $posOrder = $this->posOrder->create($pos_data);
                    
                    if (isset($posOrder->id) && $posOrder->id && isset($posOutlet->id)) {
                        $shipment_product = [];
                        $invoice_product = [];
                        
                        // Create Shipment and Generate Invoice
                        foreach ($order->items as $key => $item) {
                            $shipment_product[$item->id][$posOutlet->inventory_source_id] = $item['qty_ordered'];
                            
                            $invoice_product[$item->id] = $item['qty_ordered'];
                        }

                        if ( count($data['order_items']) != $for_shipping_count ) {
                            $shipment_order = [
                                    'shipment'  => [
                                        'carrier_title' => 'Pick Up',
                                        'track_number'  => rand(pow(10, 4), pow(10, 5)-1),
                                        'source'        => $posOutlet->inventory_source_id,
                                        'items'         => $shipment_product,
                                        'email_sent'    => 1,
                                    ],
                                    'order_id'  => $posOrder->order_id
                            ];
                            $this->shipment->create($shipment_order);
                        }

                        $invoice_order = [
                            'invoice' => [
                                'items'         => $invoice_product,
                                'email_sent'    => 1,
                            ],
                            'order_id' => $posOrder->order_id
                        ];
                        $this->invoice->create($invoice_order);

                        $order_barcode = '';
                        if ( $posOrder->order_barcode_path ) {
                            $order_barcode = asset('/storage' . $posOrder->order_barcode_path);
                        }

                        return response()->json([
                            'status'        => true,
                            'message'       => 'Success: Pos Order has been generated successfully!',
                            'order_id'      => $posOrder->order_id,
                            'order_barcode' => $order_barcode,
                            'route'         => 'pos_sales_history'
                        ]);
                    } else {
                        return response()->json([
                            'status'    => false,
                            'message'   => 'Warning: Invalid Pos Order!'
                        ]);
                    }
                } else {
                    return response()->json([
                        'status'    => false,
                        'message'   => 'Warning: Invalid Order Parameters!'
                    ]);
                }
            } else {
                auth()->guard('posuser')->logout();

                return response()->json([
                    'status'    => false,
                    'route'     => 'pos_login',
                    'user_data' => array()
                ]);    
            }
        } else {
            auth()->guard('posuser')->logout();

            return response()->json([
                'status'    => false,
                'route'     => 'pos_login',
                'user_data' => array()
            ]);
        }
    }

    public function getOrders() {
        $params = request()->input();
        
        if ( auth()->guard('posuser')->check() ) {

            $pos_user = auth()->guard('posuser')->user();
            
            if ( !isset($params['user_id']) || (isset($params['user_id']) && ($pos_user->id != $params['user_id']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid User'
                ]);
            }

            if ( $pos_user->outlet_id ) {
                $posOutlet = $this->posOutlet->find($pos_user->outlet_id);
                if ( $posOutlet->id && $posOutlet->id == $pos_user->outlet_id) {
                    $params = request()->merge(['outlet_id' => $posOutlet->id])->all();

                    if (isset($params['filter_drawer_id']) && $params['filter_drawer_id']) {
                        $posDrawer = $this->posDrawer->find($params['filter_drawer_id']);

                        if (($posDrawer->user_id && ($posDrawer->user_id == $params['user_id']) ) && $posDrawer->outlet_id == $params['outlet_id']) {
                            $params = request()->merge(['filter_drawer_date' => $posDrawer->created_at])->all();
                        }
                    }
                    
                    return OrderResource::collection($this->posOrder->getAll());
                }
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Invalid User'
            ]);    
        } 
    }

    public function getTaxPercentage($tax_category_id, $posOutlet)
    {
        $tax_percent = 0;
        $taxCategory = $this->taxCategory->find($tax_category_id);

        if ($taxCategory) {
            $taxRates = $taxCategory->tax_rates()->where([
                'state' => $posOutlet->state,
                'country' => $posOutlet->country,
            ])->orderBy('tax_rate', 'desc')->get();
                  
            foreach ($taxRates as $rate) {
                $haveTaxRate = false;
                if (! $rate->is_zip) {
                    if ($rate->zip_code == '*' || $rate->zip_code == $posOutlet->postcode){
                        $haveTaxRate = true;
                    }
                } else {
                    if ($posOutlet->postcode >= $rate->zip_from && $posOutlet->postcode <= $rate->zip_to) {
                        $haveTaxRate = true;
                    }
                }
                if ($haveTaxRate) {
                    $tax_percent = $rate->tax_rate;
                    break;
                }
            }
        }
        return $tax_percent;
    }
}