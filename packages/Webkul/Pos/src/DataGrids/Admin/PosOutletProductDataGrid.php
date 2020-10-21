<?php

namespace Webkul\Pos\DataGrids\Admin;

use DB;
use Webkul\Ui\DataGrid\DataGrid;
use Webkul\Pos\Repositories\PosOutletRepository;

/**
 * PosOutletProduct DataGrid
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosOutletProductDataGrid extends DataGrid
{
    protected $sortOrder = 'desc'; //asc or desc

    protected $index = 'product_id';

    /**
     * PosOutlet Repository object
     *
     * @var Object
     */
    protected $posOutlet;

    /**
     * Create a new repository instance.
     *
     * @param  Webkul\Pos\Repositories\PosOutletRepository $posOutlet
     * @return void
     */
    public function __construct(PosOutletRepository $posOutlet)
    {
        parent::__construct();
        
        $this->posOutlet = $posOutlet;
    }

    public function prepareQueryBuilder()
    {
        $locale = request('locale') ?: app()->getLocale();

        $posOutlet = $this->posOutlet->find(request('id'));
        
        $queryBuilder = DB::table('product_flat as pf')
            ->leftJoin('products as p', 'pf.product_id', '=', 'p.id')
            ->leftJoin('product_images as pi', 'p.id', '=', 'pi.product_id')
            ->leftJoin('product_inventories as pq', 'pf.product_id', '=', 'pq.product_id')

            ->addSelect(
                DB::raw('p.id as product_id'),
                DB::raw('pi.path as product_image'),
                'p.type',
                'ot.inventory_source_id as pos_inventory_source_id',
                'pf.name as product_name',
                'pf.sku as product_sku',
                'pf.price as product_price',
                DB::raw('SUM(DISTINCT ' . DB::getTablePrefix() . 'pq.qty) as product_quantity'),
                'pf.status as product_status'
            )
            ->whereNull('p.parent_id')
            ->where('pf.visible_individually', 1);

            $queryBuilder = $queryBuilder->leftJoin('pos_outlet_product as op', function($qb) use($posOutlet) {
                $qb->on('op.product_id', '=', 'p.id')
                ->leftJoin('pos_outlets as ot', 'op.outlet_id', '=', 'ot.id')
                ->where('op.outlet_id', $posOutlet->id);
                });

            if ($locale !== 'all') {
                $queryBuilder = $queryBuilder->where('pf.locale', $locale);
            }

            $queryBuilder
            ->addSelect('op.status as pos_status')
            ->groupBy('p.id');
            
        // dd($queryBuilder->get());
        // dd($queryBuilder->toSql());
        $this->addFilter('product_id', 'p.id');
        $this->addFilter('product_name', 'pf.name');
        $this->addFilter('product_sku', 'pf.sku');
        $this->addFilter('product_price', 'pf.price');
        $this->addFilter('product_quantity', 'pq.qty');
        $this->addFilter('product_status', 'pf.status');
        $this->addFilter('pos_status', 'op.status');

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index' => 'product_id',
            'label' => trans('pos::app.admin.users.outlets.assign.product-id'),
            'type' => 'number',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'product_image',
            'label' => trans('pos::app.admin.users.outlets.assign.product-image'),
            'type' => 'html',
            'searchable' => false,
            'sortable' => false,
            'filterable' => false,
            'closure' => true,
            'wrapper' => function($row) {
                if ($row->product_image)
                    return '<img src=' . url('cache/small/' . $row->product_image) . ' class="img-thumbnail" width="50px" height="50px" />';

            }
        ]);

        $this->addColumn([
            'index' => 'product_name',
            'label' => trans('pos::app.admin.users.outlets.assign.product-name'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'product_sku',
            'label' => trans('pos::app.admin.users.outlets.assign.product-sku'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'product_price',
            'label' => trans('pos::app.admin.users.outlets.assign.product-price'),
            'type' => 'number',
            'sortable' => true,
            'searchable' => true,
            'filterable' => true,
            'closure' => true,
            'wrapper' => function($row) {
                if ( $row->product_price ) {
                    return $row->product_price;
                } else {
                    return '0.00';
                }
            }
        ]);

        $this->addColumn([
            'index' => 'product_quantity',
            'label' => trans('pos::app.admin.products.product-quantity'),
            'type' => 'number',
            'sortable' => true,
            'searchable' => false,
            'filterable' => true,
            'closure' => true,
            'wrapper' => function($row) {
                return app('Webkul\Pos\Repositories\PosOutletProductRepository')->getProductTotalQty($row->product_id);
            }
        ]);

        $this->addColumn([
            'index' => 'product_status',
            'label' => trans('pos::app.admin.users.outlets.assign.product-status'),
            'type' => 'boolean',
            'sortable' => true,
            'searchable' => false,
            'filterable' => true,
            'closure' => true,
            'wrapper' => function($row) {
                if ($row->product_status == 1)
                    return '<span class="badge badge-md badge-success">'.trans('pos::app.admin.users.outlets.assign.active').'</span>';
                else
                    return '<span class="badge badge-md badge-warning">'.trans('pos::app.admin.users.outlets.assign.inactive').'</span>';
            }
        ]);

        $this->addColumn([
            'index' => 'pos_status',
            'label' => trans('pos::app.admin.users.outlets.assign.pos-status'),
            'type' => 'boolean',
            'sortable' => true,
            'searchable' => false,
            'closure' => true,
            'wrapper' => function($row) {
                return '<status-container :product_id="'.$row->product_id.'" :pos_status="\'' . $row->pos_status . '\'" :outlet_id="\'' . request('id') . '\'"></status-container>';
            }
        ]);
    }

    public function prepareMassActions() {
        $this->addMassAction([
            'type' => 'update',
            'label' => trans('pos::app.admin.users.outlets.assign.mass-options'),
            'action' => route('admin.pos.outlets.massassign'),
            'method' => 'PUT',
            'options' => [
                trans('pos::app.admin.users.outlets.assign.mass-option-status') => 'status',
            ]
        ]);

        $this->addMassAction([
            'type' => 'delete',
            'label' => trans('pos::app.admin.users.outlets.mass-remove-assign'),
            'action' => route('admin.pos.outlets.massunassign'),
            'method' => 'DELETE'
        ]);

        $this->enableMassAction = true;
    }

}