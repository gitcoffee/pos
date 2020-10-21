<?php

namespace Webkul\Pos\Repositories;

use Webkul\Core\Eloquent\Repository;
use DB;

/**
 * PosTable Repository
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosTableRepository extends Repository
{

    public function model() {
        return 'Webkul\Pos\Contracts\PosTable';
    }

    /**
     * @return Collection
     */
    public function getAll()
    {
        $params = request()->input();
        
        $results = app('Webkul\Pos\Repositories\PosTableRepository')->scopeQuery(function($query) use($params) {

            $qb = $query->distinct()
                    ->addSelect('pos_restaurant_tables.*')
                    ->addSelect(DB::raw('CONCAT(pos_users.firstname, " ", pos_users.lastname) as agent_name'))
                    ->leftJoin('pos_users', 'pos_restaurant_tables.agent_id', '=', 'pos_users.id')
                    ->where('pos_restaurant_tables.status', 1);

            if (isset($params['user_id']) && $params['user_id']) {
                $qb->where('pos_restaurant_tables.agent_id', $params['user_id']);
            }
            
            if (isset($params['filter_name']) && $params['filter_name']) {
                $qb->where('pos_restaurant_tables.name', 'LIKE', '%' . urldecode($params['filter_name']) .'%');
            }

            return $qb->orderBy('pos_restaurant_tables.position')->groupBy('pos_restaurant_tables.id');
        })->paginate(isset($params['limit']) ? $params['limit'] : 10);
        
        return $results;
    }
}