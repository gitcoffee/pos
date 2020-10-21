<?php

namespace Webkul\Pos\DataGrids\Admin;

use Webkul\Ui\DataGrid\DataGrid;
use DB;

/**
 * PosTableBooking DataGrid
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosTableBookingDataGrid extends DataGrid
{
    protected $sortOrder = 'desc'; //asc or desc

    protected $index = 'booking_order_id';

    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('pos_restaurant_table_bookings as bo')
            ->leftJoin('pos_restaurant_tables as pro', 'bo.table_id', '=', 'pro.id')
            ->leftJoin('pos_users as pu', 'bo.agent_id', '=', 'pu.id')
            ->leftJoin('orders as o', 'bo.order_id', '=', 'o.id')
            ->addSelect('bo.booking_id as booking_id', 'bo.customer_name as customer_name', 'pro.name as table_name', 'pro.type as table_type', 'bo.booked_seat', 'bo.order_id as booking_order_id', 'bo.order_id as booked_order_id', 'bo.status as booking_status', 'bo.booked_date', 'bo.created_at as booking_created_at')
            ->addSelect(DB::raw('CONCAT(pu.firstname, " ", pu.lastname) as agent_name'));
            // ->where('bo.status', 0)
            // ->whereNotNull('bo.order_id');
        
        $this->addFilter('booking_id', 'bo.booking_id');
        $this->addFilter('customer_name', 'bo.customer_name');
        $this->addFilter('table_name', 'pro.name');
        $this->addFilter('table_type', 'pro.type');
        $this->addFilter('booked_seat', 'bo.booked_seat');
        $this->addFilter('agent_name', DB::raw('CONCAT(pu.firstname, " ", pu.lastname)'));
        $this->addFilter('booking_order_id', 'bo.order_id');
        $this->addFilter('booked_date', 'bo.booked_date');
        $this->addFilter('booking_created_at', 'prt.created_at');
        
        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index' => 'booking_order_id',
            'label' => trans('pos::app.admin.restaurants.bookings.order-id'),
            'type' => 'number',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
            'closure' => true,
            'wrapper' => function($row) {
                if (! $row->booking_order_id )
                    return '-';
                else
                    return $row->booking_order_id;
            }
        ]);

        $this->addColumn([
            'index' => 'booking_id',
            'label' => trans('pos::app.admin.restaurants.bookings.booking-id'),
            'type' => 'number',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'customer_name',
            'label' => trans('pos::app.admin.restaurants.bookings.booked-by'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'table_name',
            'label' => trans('pos::app.admin.restaurants.tables.name'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'table_type',
            'label' => trans('pos::app.admin.restaurants.tables.type'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
            'closure' => true,
            'wrapper' => function($row) {
                if ($row->table_type == 'circle')
                    return '<span class="table-circle">' . trans('pos::app.admin.system.restaurant.circle') . '</span>';
                elseif ($row->table_type == 'square')
                    return '<span class="table-square">' . trans('pos::app.admin.system.restaurant.square') . '</span>';
                elseif ($row->table_type == 'curved-square')
                    return '<span class="table-curved-square">' . trans('pos::app.admin.system.restaurant.curved-square') . '</span>';
            }
        ]);

        $this->addColumn([
            'index' => 'booked_seat',
            'label' => trans('pos::app.admin.restaurants.bookings.booked-seat'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'booked_date',
            'label' => trans('pos::app.admin.restaurants.bookings.booked-date'),
            'type' => 'date',
            'sortable' => true,
            'searchable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'booking_created_at',
            'label' => trans('pos::app.admin.restaurants.tables.date_added'),
            'type' => 'datetime',
            'sortable' => true,
            'searchable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'booked_order_id',
            'label' => trans('pos::app.admin.restaurants.bookings.actions'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
            'closure' => true,
            'wrapper' => function($row) {
                if ( $row->booked_order_id )
                    return '<div class="action"><a href=' . route('admin.sales.orders.view', $row->booked_order_id) . ' title="Booking View"><span class="icon eye-icon"></span></a></div>';
                else
                    return '<div class="badge badge-md badge-danger">' . trans('pos::app.admin.system.restaurant.pending') . '</div>';
            }
        ]);
    }
}