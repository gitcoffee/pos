<?php

namespace Webkul\Pos\DataGrids\Admin;

use Webkul\Ui\DataGrid\DataGrid;
use DB;

/**
 * PosBanks DataGrid
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosBankDataGrid extends DataGrid
{
    protected $sortOrder = 'desc'; //asc or desc

    protected $index = 'bank_id';

    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('pos_banks as pb')
            ->leftJoin('pos_users as pu', 'pb.agent_id', '=', 'pu.id')
            
            ->addSelect('pb.id as bank_id', 'pb.agent_id', 'pb.name as bank_name', 'pb.address as bank_address', 'pb.email as bank_email', 'pb.phone as bank_phone', 'pb.status as bank_status', 'pb.created_at as created_at')
            ->addSelect(DB::raw('CONCAT(pu.firstname, " ", pu.lastname) as agent_name'));
        
        $this->addFilter('bank_id', 'pb.id');
        $this->addFilter('bank_name', 'pb.name');
        $this->addFilter('bank_email', 'pb.email');
        $this->addFilter('bank_phone', 'pb.phone');
        $this->addFilter('bank_address', 'pb.address');
        $this->addFilter('agent_name', DB::raw('CONCAT(pu.firstname, " ", pu.lastname)'));
        $this->addFilter('bank_status', 'pb.status');
        $this->addFilter('created_at', 'pb.created_at');
        
        $this->setQueryBuilder($queryBuilder);

    }

    public function addColumns()
    {
        $this->addColumn([
            'index' => 'bank_id',
            'label' => trans('pos::app.admin.banks.id'),
            'type' => 'number',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'bank_name',
            'label' => trans('pos::app.admin.banks.name'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'bank_address',
            'label' => trans('pos::app.admin.banks.address'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'agent_name',
            'label' => trans('pos::app.admin.banks.agent-name'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
            'closure' => true,
            'wrapper' => function($row) {
                if ($row->agent_id)
                    return '<a href="'. route('admin.pos.users.edit', $row->agent_id) . '">' . $row->agent_name . '</a>';
                else
                    return '-';
            }
        ]);

        $this->addColumn([
            'index' => 'bank_status',
            'label' => trans('pos::app.admin.banks.status'),
            'type' => 'boolean',
            'sortable' => true,
            'searchable' => false,
            'closure' => true,
            'wrapper' => function($row) {
                if ($row->bank_status == 1)
                    return '<span class="badge badge-md badge-success">' . trans('pos::app.admin.banks.active') . '</span>';
                else
                    return '<span class="badge badge-md badge-danger">' . trans('pos::app.admin.banks.inactive') . '</span>';
            }
        ]);

        $this->addColumn([
            'index' => 'created_at',
            'label' => trans('pos::app.admin.banks.date-added'),
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
            'route' => 'admin.pos.banks.edit',
            'icon' => 'icon pencil-lg-icon'
        ]);

        $this->addAction([
            'type' => 'Delete',
            'method' => 'POST',
            'route' => 'admin.pos.banks.delete',
            'confirm_text' => trans('pos::app.admin.banks.confirm-banks'),
            'icon' => 'icon trash-icon'
        ]);

        $this->enableAction = true;
    }

    public function prepareMassActions() {
        $this->addMassAction([
            'type' => 'delete',
            'label' => trans('pos::app.admin.banks.mass-delete'),
            'action' => route('admin.pos.banks.massdelete'),
            'method' => 'DELETE'
        ]);

        $this->enableMassAction = true;
    }
}