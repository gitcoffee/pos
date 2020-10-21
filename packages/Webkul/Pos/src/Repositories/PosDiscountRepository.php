<?php

namespace Webkul\Pos\Repositories;

use Illuminate\Container\Container as App;
use DB;
use Webkul\Core\Eloquent\Repository;

/**
 * PosDiscount Repository
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosDiscountRepository extends Repository
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    public function __construct(
        App $app
    )
    {
        $this->_config = request('_config');

        parent::__construct($app);
    }
    
    public function model() {
        return 'Webkul\Pos\Contracts\PosDiscount';
    }

    /**
     * @return Collection
     */
    public function getAll()
    {
        $params = request()->input();
        
        if (isset($params['filter_cart_total'])) {
            $params['filter_cart_total'] = core()->convertToBasePrice($params['filter_cart_total']);
        }
        
        $results = app('Webkul\Pos\Repositories\PosDiscountRepository')->scopeQuery(function($query) use($params) {

                $qb = $query->distinct()
                        ->addSelect('pos_discount.*')
                        ->addSelect('pos_users.status AS user_status')
                        ->leftJoin('pos_users', 'pos_users.id', '=', 'pos_discount.user_id')
                        ->where('pos_users.status', 1)
                        ->where('pos_discount.status', 1);

                if (isset($params['filter_cart_total']) && $params['filter_cart_total']) {
                    $qb->where('pos_discount.fromprice', '<=', (float) $params['filter_cart_total']);
                    $qb->where('pos_discount.toprice', '>=', (float) $params['filter_cart_total']);
                }

                if (isset($params['user_id']) && $params['user_id']) {
                    $qb->where('pos_discount.user_id', $params['user_id']);
                }

                if (isset($params['discount_id']) && $params['discount_id']) {
                    $qb->where('pos_discount.id', $params['discount_id']);
                }

                return $qb->groupBy('pos_discount.id');
            })->paginate(isset($params['limit']) ? $params['limit'] : 10);
        
        return $results;
    }
}