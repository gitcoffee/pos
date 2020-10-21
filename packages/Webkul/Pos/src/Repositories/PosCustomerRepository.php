<?php

namespace Webkul\Pos\Repositories;

use Illuminate\Container\Container as App;
use DB;
use Webkul\Core\Eloquent\Repository;

/**
 * PosCustomer Repository
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosCustomerRepository extends Repository
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
        return 'Webkul\Customer\Contracts\Customer';
    }

    /**
     * @return Collection
     */
    public function getAll()
    {
        $params = request()->input();
        
        $results = app('Webkul\Customer\Repositories\CustomerRepository')->scopeQuery(function($query) use($params) {

                $qb = $query->distinct()
                        ->addSelect('customers.*')
                        ->leftJoin('customer_groups', 'customer_groups.id', '=', 'customers.customer_group_id')
                        ->where('customers.status', 1)
                        ->where('customers.is_verified', 1);

                if (isset($params['customer_id']) && $params['customer_id']) {
                    $qb->where('customers.id', $params['customer_id']);
                }

                if (isset($params['customer_name']) && $params['customer_name']) {
                    $qb->where(DB::raw("CONCAT(customers.first_name, ' ', customers.last_name)"), 'like', '%'. $params['customer_name'] .'%')
                        ->orWhere('customers.email', 'like', '%'. $params['customer_name'] .'%')
                        ->orWhere('customers.phone', 'like', '%'. $params['customer_name'] .'%');
                }
                
                return $qb->groupBy('customers.id');
                
            })->paginate(isset($params['limit']) ? $params['limit'] : 10);
        return $results;
    }
}