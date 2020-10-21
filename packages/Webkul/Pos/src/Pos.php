<?php

namespace Webkul\Pos;

use Webkul\Product\Repositories\ProductRepository;
use Webkul\Tax\Repositories\TaxCategoryRepository;
use Webkul\Product\Repositories\ProductBundleOptionRepository;
use Webkul\Product\Repositories\ProductBundleOptionProductRepository;
use Webkul\BookingProduct\Repositories\BookingProductRepository;
use Webkul\BookingProduct\Helpers\DefaultSlot as DefaultSlotHelper;
use Webkul\BookingProduct\Helpers\AppointmentSlot as AppointmentSlotHelper;
use Webkul\BookingProduct\Helpers\RentalSlot as RentalSlotHelper;
use Webkul\BookingProduct\Helpers\EventTicket as EventTicketHelper;
use Webkul\BookingProduct\Helpers\TableSlot as TableSlotHelper;
use Webkul\Product\Helpers\ProductImage;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Pos
{
    /**
     * ProductRepository class
     *
     * @var \Webkul\Product\Repositories\ProductRepository
     */
    protected $productRepository;

    /**
     * TaxCategoryRepository class
     *
     * @var \Webkul\Tax\Repositories\TaxCategoryRepository
     */
    protected $taxCategoryRepository;

    /**
     * ProductBundleOptionRepository instance
     *
     * @var \Webkul\Product\Repositories\ProductBundleOptionRepository
     */
    protected $productBundleOptionRepository;

    /**
     * ProductBundleOptionProductRepository instance
     *
     * @var \Webkul\Product\Repositories\ProductBundleOptionProductRepository
     */
    protected $productBundleOptionProductRepository;

    /**
     * Product Image helper instance
     *
     * @var \Webkul\Product\Helpers\ProductImage
    */
    protected $productImageHelper;

    /**
     * @return array
     */
    protected $bookingHelpers = [];

    /**
     * Create a new instance.
     *
     * @param  \Webkul\Product\Repositories\ProductRepository  $productRepository
     * @param  \Webkul\Tax\Repositories\TaxCategoryRepository  $taxCategoryRepository
     * @param  \Webkul\Product\Repositories\ProductBundleOptionRepository  $productBundleOptionRepository
     * @param  \Webkul\Product\Repositories\ProductBundleOptionProductRepository  $productBundleOptionProductRepository
     * @param  \Webkul\BookingProduct\Repositories\BookingProductRepository  $bookingProductRepository
     * @param  \Webkul\BookingProduct\Helpers\DefaultSlot                    $defaultSlotHelper
     * @param  \Webkul\BookingProduct\Helpers\AppointmentSlot                $appointmentSlotHelper
     * @param  \Webkul\BookingProduct\Helpers\RentalSlot                     $rentalSlotHelper
     * @param  \Webkul\BookingProduct\Helpers\EventTicket                    $EventTicketHelper
     * @param  \Webkul\BookingProduct\Helpers\TableSlot                      $tableSlotHelper
     * @param  \Webkul\Product\Helpers\ProductImage                          $productImageHelper
     * 
     * @return void
     */
    public function __construct(
        ProductRepository $productRepository,
        TaxCategoryRepository $taxCategoryRepository,
        ProductBundleOptionRepository $productBundleOptionRepository,
        ProductBundleOptionProductRepository $productBundleOptionProductRepository,
        BookingProductRepository $bookingProductRepository,
        DefaultSlotHelper $defaultSlotHelper,
        AppointmentSlotHelper $appointmentSlotHelper,
        RentalSlotHelper $rentalSlotHelper,
        EventTicketHelper $eventTicketHelper,
        TableSlotHelper $tableSlotHelper,
        ProductImage $productImageHelper
    )
    {
        $this->productRepository = $productRepository;
        
        $this->taxCategoryRepository = $taxCategoryRepository;

        $this->productBundleOptionRepository = $productBundleOptionRepository;
        
        $this->productBundleOptionProductRepository = $productBundleOptionProductRepository;

        $this->bookingProductRepository = $bookingProductRepository;
        
        $this->bookingHelpers['default'] = $defaultSlotHelper;

        $this->bookingHelpers['appointment'] = $appointmentSlotHelper;

        $this->bookingHelpers['rental'] = $rentalSlotHelper;

        $this->bookingHelpers['event'] = $eventTicketHelper;

        $this->productImageHelper = $productImageHelper;

        $this->bookingHelpers['table'] = $tableSlotHelper;
    }

    /**
     * Return current logged in customer
     *
     * @return \Webkul\Customer\Contracts\Customer|bool
     */
    public function getOutletProductQuantity($data)
    {
        $outlet_inventory = [];
        $product = $this->productRepository->findOrFail($data->product_id);

        $channelInventorySourceId = core()->getCurrentChannel()
                                           ->inventory_sources()
                                           ->where('id', $data->pos_inventory_source_id)
                                           ->pluck('id')->first();

        switch ($data->type) {
            case 'simple':
            case 'virtual':
                if ($channelInventorySourceId == $data->pos_inventory_source_id) {
                    foreach ($product->inventories as $inventory) {
                        if ($channelInventorySourceId == $inventory->inventory_source_id) {
                            $outlet_inventory[$data->product_id] = $inventory->qty;
                        }
                    }
                }
                break;

            case 'configurable':
                if ($channelInventorySourceId == $data->pos_inventory_source_id) {
                    $product_variants = app('Webkul\Pos\Helpers\PosConfigurableOption')->getConfigurationConfig($product);
                    
                    if ( isset($product_variants['index']) && $product_variants['index']) {
                        foreach ($product_variants['index'] as $sub_product_id => $child_qty) {
                            $product = $this->productRepository->find($sub_product_id);
                            
                            foreach ($product->inventories as $inventory) {
                                if ($channelInventorySourceId == $inventory->inventory_source_id) {
                                    $outlet_inventory[$sub_product_id] = $inventory->qty;
                                }
                            }
                        }
                    }
                }
                break;

            case 'grouped':
                if ($channelInventorySourceId == $data->pos_inventory_source_id) {
                    foreach ($data->getModel()->grouped_products()->pluck('associated_product_id')->toArray() as $associated_product_id) {
                        $product = $this->productRepository->find($associated_product_id);
                            
                        foreach ($product->inventories as $inventory) {
                            if ($channelInventorySourceId == $inventory->inventory_source_id) {
                                $outlet_inventory[$associated_product_id] = $inventory->qty;
                            }
                        }
                    }
                }
                break;
            
            case 'downloadable':
            case 'bundle':
            case 'booking':
                $outlet_inventory[$data->product_id] = 0;
                break;
            
            default:
                if ($channelInventorySourceId == $data->pos_inventory_source_id) {
                    foreach ($product->inventories as $inventory) {
                        if ($channelInventorySourceId == $inventory->inventory_source_id) {
                            $outlet_inventory[$data->product_id] = $inventory->qty;
                        }
                    }
                }
                break;
        }
        
        return $outlet_inventory; 
    }

    public function getGroupedAssociatedProducts($productResource, $product)
    {
        $associated_products = [];
        $group_price = 0;
        foreach ($product->grouped_products as $associate_product) {
            $associated_product_id  = $associate_product->associated_product_id;
            $productRepository      = $this->productRepository->find($associated_product_id);
            $productInstance        = $productRepository->getTypeInstance();
            $hasSpecialPrice        = $productInstance->haveSpecialPrice();

            $group_price += ($hasSpecialPrice ? $productInstance->getSpecialPrice() : $productRepository->price) * $associate_product->qty;

            $associated_products[$associated_product_id]    = $productRepository->toArray();
            $product_related_data = [
                'tax_percent'       => 0,
                'image'             => $productInstance->getBaseImage($productRepository),
                'sort_order'        => $associate_product->sort_order,
                'qty'               => $associate_product->qty,
                'converted_price'   => core()->convertPrice($productRepository->price, core()->getCurrentCurrencyCode()),
                'formated_price'    => core()->currency($productRepository->price),
                'special_price'     => $hasSpecialPrice ? $productInstance->getSpecialPrice() : 0,
                'converted_special_price'   => $hasSpecialPrice ? 
                    core()->convertPrice(
                        $productInstance->getSpecialPrice(),
                        core()->getCurrentCurrencyCode()
                    ) : 0,
                'formated_special_price'    => $hasSpecialPrice ? core()->currency($productInstance->getSpecialPrice()) : 0,
            ];

            if ( $associate_product->tax_category_id ) {
                $product_related_data['tax_percent'] = $this->getTaxPercentage($productResource, $associate_product->tax_category_id);
            }

            $associated_products[$associated_product_id] = array_merge($associated_products[$associated_product_id], $product_related_data);
        }
        usort ($associated_products, function($a, $b) {
            if ($a['sort_order'] == $b['sort_order']) {
                return 0;
            }
            return ($a['sort_order'] < $b['sort_order']) ? -1 : 1;
        });
        
        $associated_products['group_price'] = $group_price;
        return $associated_products;
    }

    public function getDownloadLinks($product)
    {
        $download_options = [];
        $download_sampples = [];
        foreach($product->downloadable_samples as $sample_link) {
            $download_sampples[$sample_link->id] = $sample_link->toArray();
            
            foreach ($sample_link->translations as $sample_tran) {
                if ($sample_tran->locale == app()->getLocale()) {
                    $download_sampples[$sample_tran->id]['name']   = $sample_tran->title;
                }
            }
        }

        $download_links = [];
        foreach ($product->downloadable_links as $download_product) {
            $download_links[$download_product->id] = $download_product->toArray();
            $download_links[$download_product->id]['converted_price']   = core()->convertPrice($download_product->price, core()->getCurrentCurrencyCode());
            $download_links[$download_product->id]['formated_price']   = core()->currency($download_product->price);
            $download_links[$download_product->id]['file_url']          = Storage::url($download_product->file);
            $download_links[$download_product->id]['sample_file_url']   = Storage::url($download_product->sample_file);

            foreach ($download_product->translations as $download_tran) {
                if ($download_tran->locale == app()->getLocale()) {
                    $download_links[$download_product->id]['name']   = $download_tran->title;
                }
            }
        }

        $download_options = [
            'samples'   => $download_sampples,
            'links'     => $download_links,
        ];
        
        return $download_options;
    }

    public function getDownloadProductPrice($product)
    {
        $base_price = 0;
        $product_details    = $this->productRepository->find($product['product_id']);
        $base_price         = $product_details->price;
        
        foreach ($product_details->downloadable_links as $link) {
            if (! in_array($link->id, $product['links'])) {
                continue;
            }
            $base_price += $link->price;
        }
        
        return $base_price;
    }

    public function getBookingProductPrice($product)
    {
        $base_price = 0;
        $product_details    = $this->productRepository->find($product['product_id']);
        $productInstance    = $product_details->getTypeInstance();
        $base_price         = $productInstance->getSpecialPrice();
        
        $product_details = $productInstance->getBookingProduct($product['product_id'])->toArray();
        
        if ( isset($product['booking']['renting_type']) && $product_details['type'] == 'rental' ) {
            if ( $product['booking']['renting_type'] == 'daily' ) {
                $from   = Carbon::createFromTimeString($product['booking']['date_from'] . " 00:00:00");
                $to     = Carbon::createFromTimeString($product['booking']['date_to'] . " 24:00:00");

                $base_price = $base_price + ($product_details['rental_slot']['daily_price'] * $to->diffInDays($from));
            } else if ( $product['booking']['renting_type'] == 'hourly' ) {
                $from   = Carbon::createFromTimestamp($product['booking']['slot']['from'] / 1000);
                $to     = Carbon::createFromTimestamp($product['booking']['slot']['to'] / 1000);

                $base_price = $base_price + ($product_details['rental_slot']['hourly_price'] * $to->diffInHours($from));
            }
        }
        
        return $base_price;
    }

    public function getBookingTypes($product)
    {
        $product_slots = [];
        $productInstance = $product->getTypeInstance();
        $product_slots = $productInstance->getBookingProduct($product->product_id)->toArray();

        if ( $product_slots ) {
            $bookingProduct = $this->bookingProductRepository->find($product_slots['id']);

            // Set timezone
            date_default_timezone_set(config('app.timezone'));
            
            $date_from  = $bookingProduct->available_from;
            $date_to    = $bookingProduct->available_to;
            
            switch ($product_slots['type']) {
                case 'default':
                    $product_slots['default_slot']['slots'] = [];
                    while (strtotime($date_from) <= strtotime($date_to)) {
                        $date_from = date ("Y-m-d", strtotime("+1 day", strtotime($date_from)));
                        
                        $data = $this->bookingHelpers[$bookingProduct->type]->getSlotsByDate($bookingProduct, $date_from);
                        $product_slots['default_slot']['slots'][$date_from] = $data;
                    }
                    break;
                case 'appointment':
                    $product_slots['appointment_slot']['slot_times'] = $this->bookingHelpers[$bookingProduct->type]->getWeekSlotDurations($bookingProduct);
                    break;
                case 'event':
                    $product_slots['event_dates'] = $this->bookingHelpers[$bookingProduct->type]->getEventDate($bookingProduct);
                    foreach ($product_slots['event_tickets'] as $key => $ticket) {
                        $ticket['converted_price'] = core()->convertPrice($ticket['price'], core()->getCurrentCurrencyCode());
                        $ticket['formatted_converted_price'] = core()->currency($ticket['price']);
                        $product_slots['event_tickets'][$key] = $ticket;
                    }
                    break;
                case 'table':
                    $product_slots['table_slot']['slot_times'] = $this->bookingHelpers[$bookingProduct->type]->getWeekSlotDurations($bookingProduct);
                    break;
                
                default:
                    # code...
                    break;
            }
        }
        
        return $product_slots;
    }

    public function getTaxPercentage($productResource, $tax_category_id)
    {
        $tax_percent = 0;
        $taxCategory = $this->taxCategoryRepository->findOrFail($tax_category_id);

        if ( $taxCategory ) {
            $params = [
                'country'   => $productResource->pos_country,
            ];
            if ( $productResource->pos_state != '*' ) {
                $params['state']    =   $productResource->pos_state;
            }

            $taxRates = $taxCategory->tax_rates()->where($params)->orderBy('tax_rate', 'desc')->get();
            
            foreach ($taxRates as $rate) {
                $haveTaxRate = false;
                if (! $rate->is_zip ) {
                    if (! $rate->zip_code || ($rate->zip_code == $productResource->pos_postcode) ) {
                        $haveTaxRate = true;
                    }
                } else {
                    if ( $productResource->pos_postcode >= $rate->zip_from && $productResource->pos_postcode <= $rate->zip_to ) {
                        $haveTaxRate = true;
                    }
                }
                if ( $haveTaxRate ) {
                    $tax_percent = $rate->tax_rate;
                    break;
                }
            }

            return $tax_percent;
        }
    }

    public function getBundleOptionProducts($productResource, $product)
    {
        $bundle_option_products = [];
        if ( $product->bundle_options ) {
            
            foreach ($product->bundle_options as $key => $option) {
                $bundle_option_products[$option->id] = $option->toArray();

                foreach ($option->translations as $option_tran) {
                    if ($option_tran['locale'] == app()->getLocale()) {
                        $bundle_option_products[$option->id]['label']   = $option_tran->label;
                        $bundle_option_products[$option->id]['product_bundle_option_id']   = $option_tran->product_bundle_option_id;
                    }
                }
                
                foreach ($option->bundle_option_products as $key => $option_product) {
                    $product_details    = $this->productRepository->find($option_product->product_id);
                    $productInstance    = $product_details->getTypeInstance();
                    $hasSpecialPrice    = $productInstance->haveSpecialPrice();
                    $product_image      = $product_details->images()->first();
                    $image              = '';
                    if ( isset($product_image->url) ) {
                        $image = $product_image->url;
                    }

                    $product_related_data = [
                        'name'              => $product_details->name,
                        'image'             => $image,
                        'price'             => $product_details->price,
                        'converted_price'   => core()->convertPrice($product_details->price, core()->getCurrentCurrencyCode()),
                        'formated_price'    => core()->currency($product_details->price),
                        'special_price'     => $hasSpecialPrice ? $productInstance->getSpecialPrice() : 0,
                        'converted_special_price'   => $hasSpecialPrice ? 
                            core()->convertPrice(
                                $productInstance->getSpecialPrice(),
                                core()->getCurrentCurrencyCode()
                            ) : 0,
                        'formated_special_price'    => $hasSpecialPrice ? core()->currency($productInstance->getSpecialPrice()) : 0,
                        'total_qty'         => $this->getOutletProductQuantity((object)[
                            'product_id'                => $product_details->id,
                            'type'                      => $product_details->type,
                            'pos_inventory_source_id'   => request()->input('inventory_source_id'),
                        ]),
                    ];

                    $bundle_option_products[$option['id']]['option_products'][$option_product->id] = array_merge($option_product->toArray(), $product_related_data);
                }
            }
        }
        
        return $bundle_option_products;
    }

    public function haveSufficientBundleQuantity($additional, $products)
    {
        $quantities = [];
        $result     = true;
        
        foreach ($additional['bundle_options'] as $optionId => $optionProductIds) {
            foreach ($optionProductIds as $optionProductId) {
                $optionProduct = $this->productBundleOptionProductRepository->find($optionProductId);
                
                if ( isset($optionProduct->id) ) {
                    $bundleOption = $this->productBundleOptionRepository->find($optionProduct->product_bundle_option_id);

                    if ( isset($bundleOption->id) ) {
                        
                        $cart_qty = $additional['bundle_option_qty'][$optionId];
                        if ( $bundleOption->type == 'checkbox' || $bundleOption->type == 'multiselect' ) {
                            $cart_qty = $optionProduct->qty;
                        }
                        
                        if ( isset($optionProduct->product_id) && isset($quantities[$optionProduct->product_id]) ) {
                            $quantities[$optionProduct->product_id] += $cart_qty;
                        } else {
                            if ( isset($optionProduct->product_id) ) {
                                $quantities[$optionProduct->product_id] = $cart_qty;
                            }
                        }
                    } else {
                        $result = false;
                    }
                } else {
                    $result = false;
                }
            }
        }
        
        if ( $result ) {
            if (! isset($quantities[$products['product_id']]) || ( isset($quantities[$products['product_id']]) && isset($products['quantity']) && $quantities[$products['product_id']] > $products['quantity'] ) ) {
                $result = false;
            }
        }
        
        return $result;
    }

    public function getBundleProductPrice($additional)
    {
        $base_price = 0;
        foreach ($additional['bundle_options'] as $optionId => $optionProductIds) {
            foreach ($optionProductIds as $optionProductId) {
                $optionProduct = $this->productBundleOptionProductRepository->find($optionProductId);
                
                if ( isset($optionProduct->id) ) {
                    $product_details    = $this->productRepository->find($optionProduct->product_id);
                    $productInstance    = $product_details->getTypeInstance();

                    $bundleOption = $this->productBundleOptionRepository->find($optionProduct->product_bundle_option_id);

                    if ( isset($bundleOption->id) ) {
                        $cart_qty = $additional['bundle_option_qty'][$optionId];
                        if ( $bundleOption->type == 'checkbox' || $bundleOption->type == 'multiselect' ) {
                            $cart_qty = $optionProduct->qty;
                        }
                        $base_price += $productInstance->getSpecialPrice() * $cart_qty;
                    }
                }
            }
        }
        
        return $base_price;
    }

    public function getCustomerGroupPrice($product, $qty, $customer)
    {
        if (is_null($qty)) {
            $qty = 1;
        }

        $customerGroupId = $customer->customer_group_id;

        $customerGroupPrices = $product->customer_group_prices()->where(function ($query) use ($customerGroupId) {
            $query->where('customer_group_id', $customerGroupId)
                ->orWhereNull('customer_group_id');
        }
        )->get();

        if (!$customerGroupPrices->count()) {
            return $product->price;
        }

        $lastQty = 1;

        $lastPrice = $product->price;

        $lastCustomerGroupId = null;

        foreach ($customerGroupPrices as $price) {
            if ($price->customer_group_id != $customerGroupId && $price->customer_group_id) {
                continue;
            }

            if ($qty < $price->qty) {
                continue;
            }

            if ($price->qty < $lastQty) {
                continue;
            }

            if ($price->qty == $lastQty
                && $lastCustomerGroupId != null
                && $price->customer_group_id == null
            ) {
                continue;
            }

            if ( $price->value <= $lastPrice && $price->value_type == 'fixed' ) {
                $lastPrice = $price->value;

                $lastQty = $price->qty;

                $lastCustomerGroupId = $price->customer_group_id;
            } else {
                if ( $price->value_type == 'discount' && $price->value <= 100 ) {
                    $discounted_price = $product->price - ($product->price * $price->value) / 100;
                    
                    if ( $discounted_price <= $lastPrice ) {

                        $lastPrice = $discounted_price;
                        
                        $lastQty = $price->qty;
    
                        $lastCustomerGroupId = $price->customer_group_id;
                    }
                }
            }
        }

        return $lastPrice;
    }
    
    /**
     * Get product base image
     *
     * @param  \Webkul\Customer\Contracts\Wishlist|\Webkul\Checkout\Contracts\CartItem  $item
     * @return array
     */
    public function getConfigurableProductBaseImage($item)
    {
        if ($item instanceof \Webkul\Customer\Contracts\Wishlist) {
            if (isset($item->additional['selected_configurable_option'])) {
                $product = $this->productRepository->find($item->additional['selected_configurable_option']);
            } else {
                $product = $item->product;
            }
        } else {
            if ($item instanceof \Webkul\Customer\Contracts\CartItem) {
                $product = $item->child->product;
            } else {
                $product = $item->product;
            }
        }

        return $this->productImageHelper->getProductBaseImage($product);
    }
}