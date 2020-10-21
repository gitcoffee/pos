<?php

namespace Webkul\Pos\DataGrids\Admin;

use Webkul\Ui\DataGrid\DataGrid;
use DB;

/**
 * PosProduct DataGrid
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosProductDataGrid extends DataGrid
{
    protected $sortOrder = 'desc'; //asc or desc

    protected $index = 'product_id';

    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('product_flat as pf')
            ->leftJoin('products as p', 'pf.product_id', '=', 'p.id')
            ->leftJoin('product_images as pi', 'p.id', '=', 'pi.product_id')
            ->leftJoin('pos_product_barcode as bc', 'p.id', '=', 'bc.product_id')
            ->leftJoin('product_inventories as pq', 'pf.product_id', '=', 'pq.product_id')
            ->addSelect(DB::raw('p.id as product_id'), DB::raw('pi.path as product_image'), 'pf.name as product_name', 'p.sku as product_sku', 'bc.barcode', 'pf.price as product_price', DB::raw('SUM(pq.qty) as product_quantity'), 'pf.status')
            ->whereNull('p.parent_id')
            ->where('channel', core()->getCurrentChannelCode())
            ->where('locale', app()->getLocale())
            ->groupBy('pf.product_id');
        
        $this->addFilter('product_id', 'p.id');
        $this->addFilter('product_name', 'pf.name');
        $this->addFilter('product_sku', 'p.sku');
        $this->addFilter('product_price', 'pf.price');
        $this->addFilter('product_quantity', DB::raw('SUM(pq.qty)'));
        
        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index' => 'product_id',
            'label' => trans('pos::app.admin.products.product-id'),
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
            'index' => 'product_sku',
            'label' => trans('pos::app.admin.products.product-sku'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true
        ]);
        
        $this->addColumn([
            'index' => 'barcode',
            'label' => trans('pos::app.admin.products.barcode'),
            'type' => 'string',
            'sortable' => false,
            'searchable' => false,
            'closure' => true,
            'wrapper' => function($row) {
                if ($row->barcode)
                    return '<img src=' . asset('/storage/product/' . $row->product_id . '/' . $row->barcode . '.png') . ' width="50px" height="50px" />';
                else
                    return 'N/A';
            }
        ]);

        $this->addColumn([
            'index' => 'product_price',
            'label' => trans('pos::app.admin.products.product-price'),
            'type' => 'number',
            'sortable' => true,
            'searchable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'status',
            'label' => trans('pos::app.admin.products.product-status'),
            'type' => 'boolean',
            'sortable' => true,
            'searchable' => false,
            'closure' => true,
            'wrapper' => function($row) {
                if ($row->status == 1)
                    return '<span class="badge badge-md badge-success">' . trans('pos::app.admin.products.active') . '</span>';
                else
                    return '<span class="badge badge-md badge-danger">' . trans('pos::app.admin.products.inactive') . '</span>';
            }
        ]);
    }

    public function prepareActions() {
        $this->addAction([
            'type' => 'generate_barcode',
            'method' => 'GET',
            'route' => 'admin.pos.products.generateBarcode',
            'icon' => 'icon barcode-icon'
        ]);

        $this->addAction([
            'type' => 'print_barcode',
            'method' => 'GET',
            'route' => 'admin.pos.products.printBarcode',
            'icon' => 'icon print-icon'
        ]);

        $this->enableAction = true;
    }

    public function prepareMassActions() {
        $this->addMassAction([
            'type' => 'update',
            'label' => trans('pos::app.admin.products.mass-options'),
            'action' => route('admin.pos.products.massupdate'),
            'method' => 'PUT',
            'options' => [
                trans('pos::app.admin.products.mass-generate-barcode-btn-title') => 'generate',
                trans('pos::app.admin.products.mass-print-barcode-btn-title') => 'print'
            ]
        ]);

        $this->enableMassAction = true;
    }

}