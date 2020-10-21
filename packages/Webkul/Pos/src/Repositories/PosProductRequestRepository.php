<?php

namespace Webkul\Pos\Repositories;

use Illuminate\Container\Container as App;
use DB;
use Webkul\Core\Eloquent\Repository;
use Webkul\Pos\Repositories\PosUserRepository as PosUser;
use Webkul\Pos\Repositories\PosOutletProductRepository as PosOutletProduct;

/**
 * PosProductRequest Repository
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosProductRequestRepository extends Repository
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    protected $posUser;

    protected $posOutletProduct;

    public function __construct(
        App $app,
        PosUser $posUser,
        PosOutletProduct $posOutletProduct
    )
    {
        $this->_config = request('_config');

        $this->posUser = $posUser;
        
        $this->posOutletProduct = $posOutletProduct;

        parent::__construct($app);
    }
    
    public function model() {
        return 'Webkul\Pos\Contracts\PosProductRequest';
    }

    /**
     * @return Collection
     */
    public function getAll()
    {
        $params = request()->input();
        
        $results = app('Webkul\Product\Repositories\ProductFlatRepository')->scopeQuery(function($query) use($params) {
                $channel = request()->get('channel') ?: (core()->getCurrentChannelCode() ?: core()->getDefaultChannelCode());

                $locale = request()->get('locale') ?: app()->getLocale();

                $qb = $query->distinct()
                        ->addSelect('product_flat.name')
                        ->addSelect('product_flat.sku')
                        ->addSelect('pos_product_request.*')
                        ->leftJoin('pos_product_request', 'pos_product_request.product_id', '=', 'product_flat.product_id')
                        ->leftJoin('pos_outlet_product', 'pos_outlet_product.product_id', '=', 'pos_product_request.product_id')
                        ->leftJoin('pos_outlets', 'pos_outlets.id', '=', 'pos_outlet_product.outlet_id')
                        ->leftJoin('products', 'pos_outlet_product.product_id', '=', 'products.id')
                        ->where('pos_outlet_product.status', 1)
                        ->where('product_flat.channel', $channel)
                        ->where('product_flat.locale', $locale)
                        ->whereNotNull('product_flat.url_key');

                if (is_null(request()->input('status'))) {
                    $qb->where('product_flat.status', 1);
                }

                if (is_null(request()->input('visible_individually'))) {
                    $qb->where('product_flat.visible_individually', 1);
                }

                if (isset($params['user_id']) && $params['user_id']) {
                    $qb->where('pos_product_request.user_id', $params['user_id']);
                }

                if (isset($params['product_id']) && $params['product_id']) {
                    $qb->where('pos_product_request.product_id', $params['product_id']);
                }

                if (isset($params['send_status']) && !is_null($params['send_status'])) {
                    $qb->where('pos_product_request.send_status', $params['send_status']);
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

                $qb->leftJoin('product_flat as flat_variants', function($qb) use($channel, $locale) {
                    $qb->on('product_flat.id', '=', 'flat_variants.parent_id')
                        ->where('flat_variants.channel', $channel)
                        ->where('flat_variants.locale', $locale);
                });
                
                if (isset($params['send_status']) && $params['send_status']) {
                    return $qb->groupBy('pos_product_request.id');
                } else {
                    return $qb->groupBy('product_flat.id');
                }
                
            })->paginate(isset($params['limit']) ? $params['limit'] : 10);
            
        return $results;
    }
}