<?php

namespace Webkul\Pos\Repositories;

use Illuminate\Container\Container as App;
use Carbon\Carbon;
use Webkul\Core\Eloquent\Repository;

/**
 * PosDrawer Repository
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosDrawerRepository extends Repository
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
        return 'Webkul\Pos\Contracts\PosDrawer';
    }

    /**
     * @return Collection
     */
    public function getDrawerRecords($filter_data)
    {
        $results = app('Webkul\Pos\Repositories\PosDrawerRepository')->scopeQuery(function($query) use($filter_data) {

                $qb = $query->distinct()
                        ->addSelect('pos_user_drawer.*')
                        ->leftJoin('pos_users', 'pos_users.id', '=', 'pos_user_drawer.user_id')
                        ->leftJoin('pos_outlets', 'pos_outlets.id', '=', 'pos_users.outlet_id')
                        ->where('pos_users.status', 1)
                        ->where('pos_user_drawer.created_at', 'LIKE', '%'.date("Y-m-d").'%');

                if (isset($filter_data['user_id']) && $filter_data['user_id']) {
                    $qb->where('pos_user_drawer.user_id', $filter_data['user_id']);
                }

                if (isset($filter_data['outlet_id']) && $filter_data['outlet_id']) {
                    $qb->where('pos_user_drawer.outlet_id', $filter_data['outlet_id']);
                }

                if (isset($filter_data['today_entry']) && $filter_data['today_entry']) {
                    $qb->where('pos_user_drawer.created_at', 'LIKE', '%'.date("Y-m-d").'%');
                    $qb->orderBy('pos_user_drawer.created_at', 'DESC');
                }

                if ( isset($filter_data['duration_type']) && $filter_data['duration_type']) {
                    $qb->whereYear('pos_user_drawer.created_at', Carbon::now()->year);
                    $qb->whereMonth('pos_user_drawer.created_at', Carbon::now()->month);
                    $qb->orderBy('pos_user_drawer.created_at', 'DESC');
                }

                return $qb->groupBy('pos_user_drawer.id');
            })->paginate(isset($filter_data['limit']) ? $filter_data['limit'] : 10);
        
        return $results;
    }
}