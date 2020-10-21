<?php

namespace Webkul\Pos\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Product\Repositories\ProductRepository;
use Carbon\Carbon;

/**
 * OutletProduct JsonResource
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class OutletProduct extends JsonResource
{
    /**
     * TaxCategoryRepository model
     *
     * @var mixed
     */
    protected $taxCategory;

    /**
     * ProductRepository model
     *
     * @var mixed
     */
    protected $productDetails;
    /**
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct($resource)
    {
        $this->posConfigurableOptionHelper = app('Webkul\Pos\Helpers\PosConfigurableOption');
        
        $this->product = app(ProductRepository::class);

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
        $productInstance = $this->getTypeInstance();
        $product    = $this->product->findBySlugOrFail($this->url_key);

        if ($product->type == 'configurable') {
            $images     = bagisto_pos()->getConfigurableProductBaseImage($product);
        } else {
            $images     = $productInstance->getBaseImage($product);
        }

        $product_variants = [];
        if ($product->type == 'configurable') {
            $product_variants = $this->posConfigurableOptionHelper->getConfigurationConfig($product);
        }

        $grouped_associated_products = [];
        if ($product->type == 'grouped') {
            $grouped_associated_products = bagisto_pos()->getGroupedAssociatedProducts($this, $product);
            $this->price = $grouped_associated_products['group_price'];
            unset($grouped_associated_products['group_price']);
        }

        $downloadable_links = [];
        if ($product->type == 'downloadable') {
            $downloadable_links = bagisto_pos()->getDownloadLinks($product);
        }

        $bundle_option_products = [];
        if ($product->type == 'bundle') {
            $bundle_option_products = bagisto_pos()->getBundleOptionProducts($this, $product);
        }

        $booking_options = [];
        if ($product->type == 'booking') {
            $booking_options = bagisto_pos()->getBookingTypes($product);
        }
        
        $tax_percent = 0;
        if ( $product->product->tax_category_id ) {
            $tax_percent = bagisto_pos()->getTaxPercentage($this, $product->product->tax_category_id);
        }
        
        $customerGroupPrices = [];;
        $customer_group_prices = $product->product->customer_group_prices()->get();
        if ( $customer_group_prices ) {
            foreach( $customer_group_prices as $key => $customer_group_price) {
                $customerGroupPrices[$key] = $customer_group_price->toArray();
                $customerGroupPrices[$key]['converted_value'] = core()->convertPrice($customerGroupPrices[$key]['value'], core()->getCurrentCurrencyCode());
            }
        }        
        
        return [
            'id'                        => $this->product_id,
            'sku'                       => $this->sku,
            'type'                      => $this->type,
            'weight'                    => $this->weight,
            'name'                      => $this->name,
            'minimal_price'             => core()->convertPrice($productInstance->getMinimalPrice(), core()->getCurrentCurrencyCode()),
            'formated_minimal_price'    => core()->currency($productInstance->getMinimalPrice()),
            'price'                     => $this->price,
            'converted_price'           => core()->convertPrice($this->price, core()->getCurrentCurrencyCode()),
            'formated_price'            => core()->currency($this->price),
            'customerGroupPrices'       => $customerGroupPrices,
            'description'               => $this->description,
            'quantity'                  => json_encode(bagisto_pos()->getOutletProductQuantity($this)),
            'pos_status'                => $this->pos_status,
            'product_barcode'           => $this->product_barcode,
            'pos_inventory_source_id'   => $this->pos_inventory_source_id,
            'tax_category_id'           => $product->product->tax_category_id,
            'tax_percent'               => $tax_percent,
            'base_image'                => $images,
            'variants'                  => json_encode($product_variants),
            'grouped_associated'        => json_encode($grouped_associated_products),
            'downloadable_links'        => json_encode($downloadable_links),
            'bundle_option_products'    => json_encode($bundle_option_products),
            'booking_options'           => json_encode($booking_options),
            'special_price'             => $this->when(
                $productInstance->haveSpecialPrice(),
                $productInstance->getSpecialPrice()
            ),
            'converted_special_price'   => $this->when(
                $productInstance->haveSpecialPrice(),
                core()->convertPrice(
                    $productInstance->getSpecialPrice(),
                    core()->getCurrentCurrencyCode()
                )
            ),
            'formated_special_price'    => $this->when(
                $productInstance->haveSpecialPrice(),
                core()->currency($productInstance->getSpecialPrice())
            ),
            'created_at'                => $this->created_at,
            'updated_at'                => $this->updated_at,
        ];
    }
}