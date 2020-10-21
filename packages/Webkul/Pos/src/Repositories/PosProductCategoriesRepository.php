<?php

namespace Webkul\Pos\Repositories;

use Webkul\Core\Eloquent\Repository;

/**
 * PosProductCategories Repository
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosProductCategoriesRepository extends Repository
{
    public function model() {
        return 'Webkul\Pos\Contracts\PosProductCategories';
    }

    public function getAll($filter_data)
    {
        $params = request()->input();

        $results = app('Webkul\Pos\Repositories\PosOutletProductRepository')->scopeQuery(function($query) use($params, $filter_data) {

            $qb = $query->distinct()
                    ->addSelect('product_categories.*')
                    ->addSelect('pos_users.id AS user_id')
                    ->addSelect('pos_outlets.id AS outlet_id')
                    ->leftJoin('pos_outlets', 'pos_outlets.id', '=', 'pos_outlet_product.outlet_id')
                    ->leftJoin('pos_users', 'pos_users.outlet_id', '=', 'pos_outlets.id')
                    ->leftJoin('product_categories', 'product_categories.product_id', '=', 'pos_outlet_product.product_id')
                    ->leftJoin('categories', 'categories.id', '=', 'product_categories.category_id')
                    ->where('pos_users.id', $params['user_id'])
                    ->whereNotNull('product_categories.product_id')
                    ->whereNotNull('product_categories.category_id')
                    ->where('pos_users.outlet_id', $params['outlet_id'])
                    ->where('pos_outlet_product.status', 1);

            return $qb->orderBy('product_categories.category_id');
        })->paginate(isset($filter_data['limit']) ? $filter_data['limit'] : 10);
    
        return $results;
    
    }
}