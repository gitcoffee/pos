<?php

namespace Webkul\Pos\DataGrids\Admin;

use Webkul\Ui\DataGrid\DataGrid;
use DB;

/**
 * PosOutlet DataGrid
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosOutletDataGrid extends DataGrid
{
    protected $sortOrder = 'desc'; //asc or desc

    protected $index = 'outlet_id';

    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('pos_outlets')
            ->leftJoin('inventory_sources', 'pos_outlets.inventory_source_id', '=', 'inventory_sources.id')
            ->leftJoin('channel_inventory_sources', 'pos_outlets.id', '=', 'channel_inventory_sources.inventory_source_id')
            ->addSelect('pos_outlets.id as outlet_id', 'pos_outlets.name as outlet_name', 'pos_outlets.created_at', 'pos_outlets.status as outlet_status', 'inventory_sources.name as inventory_source_name')
            ->where('channel_inventory_sources.channel_id', core()->getCurrentChannel()->id);
        
        $this->addFilter('outlet_id', 'pos_outlets.id');
        $this->addFilter('outlet_name', 'pos_outlets.name');
        $this->addFilter('outlet_status', 'pos_outlets.status');
        $this->addFilter('inventory_source_name', 'inventory_sources.name');

        $this->setQueryBuilder($queryBuilder);

    }

    public function addColumns()
    {
        $this->addColumn([
            'index' => 'outlet_id',
            'label' => trans('pos::app.admin.users.outlets.id'),
            'type' => 'number',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'outlet_name',
            'label' => trans('pos::app.admin.users.outlets.name'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'inventory_source_name',
            'label' => trans('pos::app.admin.users.outlets.inventory-source-name'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'created_at',
            'label' => trans('pos::app.admin.users.outlets.date_added'),
            'type' => 'datetime',
            'sortable' => true,
            'searchable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'outlet_status',
            'label' => trans('pos::app.admin.users.outlets.status'),
            'type' => 'boolean',
            'sortable' => true,
            'searchable' => false,
            'closure' => true,
            'wrapper' => function($row) {
                if ($row->outlet_status == 1)
                    return '<span class="badge badge-md badge-success">' . trans('pos::app.admin.users.outlets.active') . '</span>';
                else
                    return '<span class="badge badge-md badge-danger">' . trans('pos::app.admin.users.outlets.inactive') . '</span>';
            }
        ]);
    }

    public function prepareActions() {
        $this->addAction([
            'type' => 'Edit',
            'method' => 'GET', //use post only for redirects only
            'route' => 'admin.pos.outlets.edit',
            'icon' => 'icon pencil-lg-icon'
        ]);

        $this->addAction([
            'type' => 'Edit',
            'method' => 'GET', //use post only for redirects only
            'route' => 'admin.pos.outlets.assign',
            'icon' => 'icon listing-icon'
        ]);

        $this->addAction([
            'type' => 'Delete',
            'method' => 'POST', //use post only for requests other than redirects
            'route' => 'admin.pos.outlets.delete',
            'icon' => 'icon trash-icon'
        ]);

        $this->enableAction = true;
    }

    public function prepareMassActions() {
        $this->addMassAction([
            'type' => 'delete',
            'label' => trans('pos::app.admin.users.outlets.mass-delete'),
            'action' => route('admin.pos.outlets.massdelete'),
            'method' => 'DELETE'
        ]);

        $this->enableMassAction = true;
    }

}