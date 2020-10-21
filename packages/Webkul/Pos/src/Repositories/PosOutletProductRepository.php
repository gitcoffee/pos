<?php

namespace Webkul\Pos\Repositories;

use Illuminate\Container\Container as App;
use DB;
use Webkul\Core\Eloquent\Repository;
use Webkul\Pos\Repositories\PosOutletRepository as PosOutlet;
use Webkul\Product\Repositories\ProductRepository as Product;
use Webkul\Product\Repositories\ProductInventoryRepository as ProductInventory;
use Webkul\Product\Repositories\ProductImageRepository as ProductImage;

/**
 * PosOutletProduct Repository
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosOutletProductRepository extends Repository
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    protected $posOutlet;

    protected $product;

    protected $productInventory;

    protected $productImage;

    public function __construct(
        App $app,
        PosOutlet $posOutlet,
        Product $product,
        ProductInventory $productInventory,
        ProductImage $productImage
    )
    {
        $this->_config = request('_config');

        $this->posOutlet = $posOutlet;

        $this->product = $product;

        $this->productInventory = $productInventory;
        
        $this->productImage = $productImage;

        parent::__construct($app);
    }
    
    public function model() {
        return 'Webkul\Pos\Contracts\PosOutletProduct';
    }

    public function assignToOutlet(array $data, $outlet_id)
    {
        $productIds = explode(',', $data['indexes']);
        foreach ($productIds as $product_id) {
            $outletProduct = $this->findWhere(['product_id' => $product_id, 'outlet_id' => $outlet_id])->first();

            if (isset($outletProduct->product_id) && $outletProduct->product_id) {
                $this->update(['status'  => true,], $outletProduct->id);
            } else {
                $this->create([
                    'product_id' => $product_id,
                    'outlet_id'  => $outlet_id,
                    'status'  => true,
                ]);
            }
        }
    }

    public function singleAssignToOutlet(array $data, $outlet_id)
    {
        $productIds = explode(',', $data['indexes']);
        foreach ($productIds as $key => $product_id) {
            $outletProduct = $this->findWhere(['product_id' => $product_id, 'outlet_id' => $outlet_id]);
            
            if (isset($outletProduct[0]->product_id) && $outletProduct[0]->product_id) {
                $this->update([
                    'status'    => $data['status'],
                ], $outletProduct[0]->id);
                return true;
            } else {
                $this->create([
                    'product_id' => $product_id,
                    'outlet_id' => $outlet_id,
                    'status'    => $data['status'],
                ]);
                return true;
            }
        }
    }

    public function getProductTotalQty($product_id)
    {
        $quantity = false;

        $product = $this->product->find($product_id);

        if ( isset($product['type']) && $product['type'] == 'simple' || $product['type'] == 'virtual' ) {
            
            $productInventory = $this->productInventory->findWhere(['product_id' => $product_id], ['qty']);

            if (isset($productInventory[0]['qty'])) {
                $quantity = $productInventory[0]['qty'];
            }
        } else {
            $product_qty = 0;

            $products = $this->product->findWhere(['parent_id' => $product_id]);

            foreach ($products as $key => $sub_product) {
                $productInventory = $this->productInventory->findWhere(['product_id' => $sub_product['id']], ['qty']);

                if (isset($productInventory[0]['qty'])) {
                    $product_qty += $productInventory[0]['qty'];
                }
            }
            $quantity = $product_qty;
        }
        return $quantity;
    }

    /**
     * @param integer $categoryId
     * @return Collection
     */
    public function getAll($categoryId = null)
    {
        $params = request()->input();

        $posOutlet = $this->posOutlet->find($params['outlet_id']);
        
        if ($posOutlet->id && $posOutlet->inventory_source_id) {
            $params['inventory_source_id'] = $posOutlet->inventory_source_id;
        }
        
        $results = app('Webkul\Product\Repositories\ProductFlatRepository')->scopeQuery(function($query) use($params, $categoryId) {
                
            $channel = request()->get('channel') ?: (core()->getCurrentChannelCode() ?: core()->getDefaultChannelCode());

            $locale = request()->get('locale') ?: app()->getLocale();

            $qb = $query->distinct()
                    ->addSelect('product_flat.*')
                    ->addSelect('pos_outlets.id AS pos_outlet_id')
                    ->addSelect('pos_outlets.inventory_source_id AS pos_inventory_source_id')
                    ->addSelect('pos_outlets.country AS pos_country')
                    ->addSelect('pos_outlets.state AS pos_state')
                    ->addSelect('pos_outlets.postcode AS pos_postcode')
                    ->addSelect('pos_outlet_product.status AS pos_status')
                    ->addSelect('pos_product_barcode.barcode AS product_barcode')
                    ->addSelect('product_inventories.qty AS pos_quantity')
                    ->leftJoin('pos_outlet_product', 'pos_outlet_product.product_id', '=', 'product_flat.product_id')
                    ->leftJoin('pos_outlets', 'pos_outlets.id', '=', 'pos_outlet_product.outlet_id')
                    ->leftJoin('inventory_sources', 'pos_outlets.inventory_source_id', '=', 'inventory_sources.id')
                    ->leftJoin('products', 'pos_outlet_product.product_id', '=', 'products.id')
                    ->leftJoin('product_categories', 'products.id', '=', 'product_categories.product_id')
                    ->leftJoin('pos_product_barcode', 'pos_outlet_product.product_id', '=', 'pos_product_barcode.product_id')
                    
                    ->leftJoin('product_inventories', 'pos_outlet_product.product_id', '=', 'product_inventories.product_id')
                    ->where('pos_outlet_product.status', 1)
                    ->where('product_flat.channel', $channel)
                    ->where('product_flat.locale', $locale)
                    ->whereNotNull('product_flat.url_key');

            if (isset($params['category_id']) && $params['category_id']) {
                $qb->where('product_categories.category_id', $params['category_id']);
            }

            if (isset($categoryId) && $categoryId) {
                $qb->where('product_categories.category_id', $categoryId);
            }

            if (isset($params['outlet_id']) && $params['outlet_id']) {
                $qb->where('pos_outlet_product.outlet_id', $params['outlet_id']);
            }
            
            if (isset($params['filter_name']) && $params['filter_name']) {
                $qb->where(function ($query) use ($params, $locale) {
                    $query->where('product_flat.name', 'like', '%'. strtolower($params['filter_name']) .'%')
                        ->where('product_flat.locale', '=', $locale);
                })->orWhere(function($query) use ($params, $locale) {
                    $query->where('product_flat.sku', 'like', '%'. strtolower($params['filter_name']) .'%')
                    ->where('product_flat.locale', '=', $locale);
                });
            }

            if (isset($params['filter_status'])) {
                if (isset($params['filter_quantity']) && $params['filter_quantity']) {
                    $qb->where('product_inventories.inventory_source_id', '=', $params['inventory_source_id']);
                    $qb->where('product_inventories.qty', '<=', (int) $params['filter_quantity']);
                }
            }
            
            if (is_null(request()->input('status'))) {
                $qb->where('product_flat.status', 1);
            }

            if (is_null(request()->input('visible_individually'))) {
                $qb->where('product_flat.visible_individually', 1);
            }

            $queryBuilder = $qb->leftJoin('product_flat as flat_variants', function($qb) use($channel, $locale) {
                $qb->on('product_flat.id', '=', 'flat_variants.parent_id')
                    ->where('flat_variants.channel', $channel)
                    ->where('flat_variants.locale', $locale);
            });

            return $qb->groupBy('product_flat.product_id');
        })->paginate(isset($params['limit']) ? $params['limit'] : 10);
        
        return $results;
    }

}