<?php

namespace Webkul\Pos\DataGrids\Admin;

use Webkul\Ui\DataGrid\DataGrid;
use DB;

/**
 * PosTable DataGrid
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosTableDataGrid extends DataGrid
{
    protected $sortOrder = 'desc'; //asc or desc

    protected $index = 'table_id';

    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('pos_restaurant_tables as prt')
            ->leftJoin('pos_users as pu', 'prt.agent_id', '=', 'pu.id')
            ->addSelect('prt.id as table_id', 'prt.name as table_name', 'prt.type as table_type', 'prt.no_of_seat', 'prt.position', 'prt.status as table_status', 'prt.created_at as table_created_at')
            ->addSelect(DB::raw('CONCAT(pu.firstname, " ", pu.lastname) as agent_name'));
        
        $this->addFilter('table_id', 'prt.id');
        $this->addFilter('table_name', 'prt.name');
        $this->addFilter('table_type', 'prt.type');
        $this->addFilter('no_of_seat', 'prt.no_of_seat');
        $this->addFilter('agent_name', DB::raw('CONCAT(pu.firstname, " ", pu.lastname)'));
        $this->addFilter('table_status', 'prt.status');
        $this->addFilter('table_created_at', 'prt.created_at');
        
        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index' => 'table_id',
            'label' => trans('pos::app.admin.restaurants.tables.id'),
            'type' => 'number',
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
            'index' => 'no_of_seat',
            'label' => trans('pos::app.admin.restaurants.tables.no-of-seat'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'position',
            'label' => trans('pos::app.admin.restaurants.tables.position'),
            'type' => 'string',
            'searchable' => false,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'agent_name',
            'label' => trans('pos::app.admin.restaurants.tables.agent-name'),
            'type' => 'string',
            'sortable' => true,
            'searchable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'table_status',
            'label' => trans('pos::app.admin.restaurants.tables.status'),
            'type' => 'boolean',
            'sortable' => true,
            'searchable' => false,
            'closure' => true,
            'wrapper' => function($row) {
                if ($row->table_status == 1)
                    return '<span class="badge badge-md badge-success">' . trans('pos::app.admin.restaurants.tables.active') . '</span>';
                else
                    return '<span class="badge badge-md badge-danger">' . trans('pos::app.admin.restaurants.tables.inactive') . '</span>';
            }
        ]);

        $this->addColumn([
            'index' => 'table_created_at',
            'label' => trans('pos::app.admin.restaurants.tables.date_added'),
            'type' => 'datetime',
            'sortable' => true,
            'searchable' => true,
            'filterable' => true
        ]);
    }

    public function prepareActions() {
        $this->addAction([
            'type' => 'Edit',
            'method' => 'GET',
            'route' => 'admin.pos.restaurants.tables.edit',
            'icon' => 'icon pencil-lg-icon'
        ]);

        $this->addAction([
            'type' => 'Delete',
            'method' => 'POST',
            'route' => 'admin.pos.restaurants.tables.delete',
            'confirm_text' => trans('pos::app.admin.restaurants.tables.confirm-users', ['resource' => 'User']),
            'icon' => 'icon trash-icon'
        ]);

        $this->enableAction = true;
    }

    public function prepareMassActions() {
        $this->addMassAction([
            'type' => 'delete',
            'label' => trans('pos::app.admin.restaurants.tables.mass-delete'),
            'action' => route('admin.pos.restaurants.tables.massdelete'),
            'method' => 'DELETE'
        ]);

        $this->enableMassAction = true;
    }
}