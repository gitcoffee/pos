<?php

namespace Webkul\Pos\DataGrids\Admin;

use Webkul\Ui\DataGrid\DataGrid;
use DB;

/**
 * PosReportDataGrid Class
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosReportDataGrid extends DataGrid
{
    protected $index = 'id';

    protected $sortOrder = 'desc'; //asc or desc

    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('orders')
                ->leftJoin('order_payment', 'order_payment.order_id', '=', 'orders.id')
                ->leftJoin('pos_order', 'pos_order.order_id', '=', 'orders.id')
                ->leftJoin('pos_outlets', 'pos_order.outlet_id', '=', 'pos_outlets.id')
                ->addSelect('orders.id','orders.increment_id as order_increment_id', 'orders.base_sub_total', 'orders.base_grand_total', 'orders.created_at', 'orders.status as order_status')
                ->addSelect('order_payment.method as payment_type', 'order_payment.method_title')
                ->addSelect('pos_order.id as sales_type', 'pos_order.order_note', 'pos_order.bank_name', 'pos_outlets.id as pos_outlet_id', 'pos_outlets.name as outlet_name');

        if (request('product')) {
            $queryBuilder->leftJoin('order_items', 'order_items.order_id', '=', 'orders.id')->where('order_items.name', 'like', '%' . urldecode(request('product')) . '%');
        }

        if (request('outlet')) {
            if ( request('outlet') == 'all') {
                $queryBuilder->whereNotNull('pos_order.id');
            } else {
                $queryBuilder->where('pos_order.outlet_id', request('outlet'));
            }
        }
        if (request('sale')) {
            if ( request('sale') == 'pos') {
                $queryBuilder->whereNotNull('pos_order.id');
            }
            if ( request('sale') == 'web') {
                $queryBuilder->whereNull('pos_order.id');
            }
        }
        if (request('bank')) {
            if ( request('bank') == 'all') {
                $queryBuilder->whereNotNull('pos_order.id');
            } else {
                $queryBuilder->where('pos_order.bank_name', request('bank'));
            }
        }
        if (request('start')) {
            $queryBuilder->where('orders.created_at', '>=', request('start'));
        }
        if (request('end')) {
            $queryBuilder->where('orders.created_at', '<=', request('end'));
        }
        
        $this->addFilter('order_increment_id', 'orders.increment_id');
        $this->addFilter('sales_type', 'pos_order.id');
        $this->addFilter('outlet_name', 'pos_outlets.name');
        $this->addFilter('order_note', 'pos_order.order_note');
        $this->addFilter('order_status', 'orders.status');
        $this->addFilter('method_title', 'order_payment.method_title');
        $this->addFilter('payment_type', 'order_payment.method');
        $this->addFilter('created_at', 'orders.created_at');

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index' => 'created_at',
            'label' => trans('pos::app.admin.reports.order-date'),
            'type' => 'datetime',
            'sortable' => true,
            'searchable' => false,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'sales_type',
            'label' => trans('pos::app.admin.reports.sale-type'),
            'type' => 'string',
            'sortable' => true,
            'searchable' => true,
            'filterable' => true,
            'closure' => true,
            'wrapper' => function ($row) {
                if ( $row->sales_type ) {
                    return '<span class="badge badge-md badge-info">POS</span>';
                } else {
                    return '<span class="badge badge-md badge-warning">Website</span>';
                }
            }
        ]);

        $this->addColumn([
            'index' => 'order_increment_id',
            'label' => trans('pos::app.admin.reports.id'),
            'type' => 'string',
            'searchable' => false,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'outlet_name',
            'label' => trans('pos::app.admin.reports.outlet'),
            'type' => 'price',
            'searchable' => false,
            'sortable' => true,
            'filterable' => true,
            'closure' => true,
            'wrapper' => function ($row) {
                if ( $row->pos_outlet_id ) {
                    return $row->outlet_name;
                } else {
                    return '-';
                }
            }
        ]);

        $this->addColumn([
            'index' => 'order_note',
            'label' => trans('pos::app.admin.reports.order-note'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'payment_type',
            'label' => trans('pos::app.admin.reports.payment-type'),
            'type' => 'price',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
            'closure' => true,
            'wrapper' => function ($row) {
                if ( $row->payment_type ) {
                    return $row->method_title;
                } else {
                    return '-';
                }
            }
        ]);

        $this->addColumn([
            'index' => 'bank_name',
            'label' => trans('pos::app.admin.reports.bank-name'),
            'type' => 'price',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
            'closure' => true,
            'wrapper' => function ($row) {
                if ( $row->bank_name ) {
                    return $row->bank_name;
                } else {
                    return '-';
                }
            }
        ]);

        $this->addColumn([
            'index' => 'order_status',
            'label' => trans('pos::app.admin.reports.status'),
            'type' => 'string',
            'sortable' => true,
            'searchable' => true,
            'closure' => true,
            'filterable' => true,
            'wrapper' => function ($value) {
                if ($value->order_status == 'processing')
                    return '<span class="badge badge-md badge-success">Processing</span>';
                else if ($value->order_status == 'completed')
                    return '<span class="badge badge-md badge-success">Completed</span>';
                else if ($value->order_status == "canceled")
                    return '<span class="badge badge-md badge-danger">Canceled</span>';
                else if ($value->order_status == "closed")
                    return '<span class="badge badge-md badge-info">Closed</span>';
                else if ($value->order_status == "pending")
                    return '<span class="badge badge-md badge-warning">Pending</span>';
                else if ($value->order_status == "pending_payment")
                    return '<span class="badge badge-md badge-warning">Pending Payment</span>';
                else if ($value->order_status == "fraud")
                    return '<span class="badge badge-md badge-danger">Fraud</span>';
            }
        ]);
    }

    public function prepareActions() {
        $this->addAction([
            'title' => 'Order View',
            'method' => 'GET', // use GET request only for redirect purposes
            'route' => 'admin.sales.orders.view',
            'icon' => 'icon eye-icon'
        ]);
    }
}