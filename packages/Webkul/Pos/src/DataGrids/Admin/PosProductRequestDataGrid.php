<?php

namespace Webkul\Pos\DataGrids\Admin;

use Webkul\Ui\DataGrid\DataGrid;
use DB;

/**
 * PosProductRequest DataGrid
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosProductRequestDataGrid extends DataGrid
{
    protected $sortOrder = 'desc'; //asc or desc

    protected $index = 'request_id';

    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('pos_product_request as pr')
            ->leftJoin('products as p', 'pr.product_id', '=', 'p.id')
            ->leftJoin('product_images as pi', 'p.id', '=', 'pi.product_id')
            ->leftJoin('product_flat as pf', 'pf.product_id', '=', 'p.id')
            ->leftJoin('pos_users as u', 'u.id', '=', 'pr.user_id')
            ->leftJoin('pos_outlets as po', 'po.id', '=', 'u.outlet_id')
            ->addSelect(DB::raw('pr.id as request_id'), DB::raw('p.id as product_id'), DB::raw('pi.path as product_image'), 'pf.name as product_name', DB::raw("CONCAT(u.firstname, ' ', u.lastname) as user_name"), 'po.name as outlet_name', 'pr.created_at as request_date', 'pr.requested_quantity as quantity', 'pr.request_status as request_status', 'pr.comment as comment')
            ->whereNull('p.parent_id')
            ->where('pr.send_status', 1)
            ->where('channel', core()->getCurrentChannelCode())
            ->where('locale', app()->getLocale())
            ->groupBy('pr.id');
        
        $this->addFilter('request_id', 'pr.id');
        $this->addFilter('product_id', 'pr.product_id');
        $this->addFilter('product_name', 'pf.name');
        $this->addFilter('user_name', DB::raw("CONCAT(u.firstname, ' ', u.lastname)"));
        $this->addFilter('outlet_name', 'po.name');
        $this->addFilter('request_date', 'pr.created_at');
        $this->addFilter('quantity', 'pr.requested_quantity');
        
        $this->setQueryBuilder($queryBuilder);

    }

    public function addColumns()
    {
        $this->addColumn([
            'index' => 'request_id',
            'label' => trans('pos::app.admin.requests.request_id'),
            'type' => 'number',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'product_image',
            'label' => trans('pos::app.admin.products.product-image'),
            'type' => 'html',
            'searchable' => false,
            'sortable' => false,
            'closure' => true,
            'wrapper' => function($row) {
                if ($row->product_image)
                    return '<img src=' . url('cache/small/' . $row->product_image) . ' class="img-thumbnail" width="50px" height="50px" />';

            }
        ]);

        $this->addColumn([
            'index' => 'product_name',
            'label' => trans('pos::app.admin.products.product-name'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'user_name',
            'label' => trans('pos::app.admin.requests.user_name'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'outlet_name',
            'label' => trans('pos::app.admin.requests.outlet_name'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'quantity',
            'label' => trans('pos::app.admin.requests.requested_qty'),
            'type' => 'number',
            'sortable' => true,
            'searchable' => false,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'request_date',
            'label' => trans('pos::app.admin.requests.request_date'),
            'type' => 'datetime',
            'sortable' => true,
            'searchable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'request_status',
            'label' => trans('pos::app.admin.requests.request_status'),
            'type' => 'boolean',
            'sortable' => true,
            'searchable' => false,
            'closure' => true,
            'wrapper' => function($row) {
                if ($row->request_status == 1)
                    return '<span class="badge badge-md badge-success">' . trans('pos::app.admin.requests.complete') . '</span>';
                else if ($row->request_status == 2)
                    return '<span class="badge badge-md badge-danger">' . trans('pos::app.admin.requests.decline') . '</span>';
                else
                    return '<span class="badge badge-md badge-warning">' . trans('pos::app.admin.requests.pending') . '</span>';
            }
        ]);
    }

    public function prepareActions() {
        $this->addAction([
            'type' => 'Edit',
            'method' => 'GET', //use post only for redirects only
            'route' => 'admin.pos.requests.view',
            'icon' => 'icon eye-icon'
        ]);

        $this->enableAction = true;
    }

    public function prepareMassActions() {
        $this->addMassAction([
            'type' => 'update',
            'label' => trans('pos::app.admin.requests.update_request_status'),
            'action' => route('admin.pos.requests.massupdate'),
            'method' => 'PUT',
            'options' => [
                trans('pos::app.admin.requests.complete') => '1',
                trans('pos::app.admin.requests.decline') => '2',
                trans('pos::app.admin.requests.pending') => '0'
            ]
        ]);

        $this->enableMassAction = true;
    }

}