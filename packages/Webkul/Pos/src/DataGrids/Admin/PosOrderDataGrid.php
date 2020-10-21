<?php

namespace Webkul\Pos\DataGrids\Admin;

use Webkul\Ui\DataGrid\DataGrid;
use DB;

/**
 * PosOrder DataGrid
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosOrderDataGrid extends DataGrid
{
    protected $sortOrder = 'desc'; //asc or desc

    protected $index = 'order_id';

    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('pos_order as po')
            ->leftJoin('orders as o', 'po.order_id', '=', 'o.id')
            ->addSelect('po.order_id as order_id')
            ->addSelect('po.order_ref_id as order_ref_id')
            ->addSelect(DB::raw('CONCAT(o.customer_first_name, " ", o.customer_last_name) AS full_name'))
            ->addSelect('o.created_at as created_at')
            ->addSelect('o.base_grand_total as base_grand_total')
            ->addSelect('o.status')
            ->groupBy('po.order_id');
        
        $this->addFilter('order_id', 'po.order_id');
        $this->addFilter('order_ref_id', 'po.order_ref_id');
        $this->addFilter('full_name', DB::raw('CONCAT(o.customer_first_name, " ", o.customer_last_name)'));
        $this->addFilter('created_at', 'o.created_at');
        $this->addFilter('base_grand_total', 'o.base_grand_total');
        $this->addFilter('status', 'o.status');

        $this->setQueryBuilder($queryBuilder);

    }

    public function addColumns()
    {
        $this->addColumn([
            'index' => 'order_id',
            'label' => trans('pos::app.admin.orders.order_id'),
            'type' => 'number',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'order_ref_id',
            'label' => trans('pos::app.admin.orders.order_ref_id'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
            'closure' => true,
            'wrapper' => function ($row) {
                if ($row->order_ref_id)
                    return $row->order_ref_id;
                else
                    return 'N/A';
            }
        ]);

        $this->addColumn([
            'index' => 'full_name',
            'label' => trans('pos::app.admin.orders.customer_name'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'created_at',
            'label' => trans('pos::app.admin.orders.order_date'),
            'type' => 'datetime',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'base_grand_total',
            'label' => trans('pos::app.admin.orders.grand_total'),
            'type' => 'price',
            'sortable' => true,
            'searchable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'status',
            'label' => trans('pos::app.admin.orders.order_status'),
            'type' => 'boolean',
            'sortable' => false,
            'searchable' => false,
            'closure' => true,
            'wrapper' => function ($value) {
                if ($value->status == 'processing')
                    return '<span class="badge badge-md badge-success">Processing</span>';
                else if ($value->status == 'completed')
                    return '<span class="badge badge-md badge-success">Completed</span>';
                else if ($value->status == "canceled")
                    return '<span class="badge badge-md badge-danger">Canceled</span>';
                else if ($value->status == "closed")
                    return '<span class="badge badge-md badge-info">Closed</span>';
                else if ($value->status == "pending")
                    return '<span class="badge badge-md badge-warning">Pending</span>';
                else if ($value->status == "pending_payment")
                    return '<span class="badge badge-md badge-warning">Pending Payment</span>';
                else if ($value->status == "fraud")
                    return '<span class="badge badge-md badge-danger">Fraud</span>';
            }
        ]);
    }

    public function prepareActions() {
        $this->addAction([
            'type' => 'View',
            'method' => 'GET', // use GET request only for redirect purposes
            'route' => 'admin.sales.orders.view',
            'icon' => 'icon eye-icon'
        ]);

        $this->enableAction = true;
    }
}