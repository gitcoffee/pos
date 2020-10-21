<?php

namespace Webkul\Pos\DataGrids\Admin;

use Webkul\Ui\DataGrid\DataGrid;
use DB;

/**
 * PosUser DataGrid
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosUserDataGrid extends DataGrid
{
    protected $sortOrder = 'desc'; //asc or desc

    protected $index = 'user_id';

    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('pos_users as pu')
            ->leftJoin('pos_outlets as po', 'pu.outlet_id', '=', 'po.id')
            
            ->addSelect('pu.id as user_id', 'pu.username as user_name', 'pu.email as user_email', 'pu.image as avatar', 'po.name as outlet_name', 'pu.status as user_status', 'pu.created_at as user_created_at');
        
        $this->addFilter('user_id', 'pu.id');
        $this->addFilter('user_name', 'pu.username');
        $this->addFilter('user_email', 'pu.email');
        $this->addFilter('outlet_name', 'po.name');
        $this->addFilter('user_status', 'pu.status');
        $this->addFilter('user_created_at', 'pu.created_at');
        
        $this->setQueryBuilder($queryBuilder);

    }

    public function addColumns()
    {
        $this->addColumn([
            'index' => 'user_id',
            'label' => trans('pos::app.admin.users.users.id'),
            'type' => 'number',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'image',
            'label' => trans('pos::app.admin.users.users.user-image'),
            'type' => 'html',
            'searchable' => false,
            'sortable' => false,
            'closure' => true,
            'wrapper' => function($row) {
                if ($row->avatar)
                    return '<img src=' . url('cache/small/' . $row->avatar) . ' class="img-thumbnail" width="50px" height="50px" />';

            }
        ]);

        $this->addColumn([
            'index' => 'user_name',
            'label' => trans('pos::app.admin.users.users.username'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'user_email',
            'label' => trans('pos::app.admin.users.users.email'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'outlet_name',
            'label' => trans('pos::app.admin.users.users.outlet-name'),
            'type' => 'string',
            'sortable' => true,
            'searchable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'user_status',
            'label' => trans('pos::app.admin.users.users.status'),
            'type' => 'boolean',
            'sortable' => true,
            'searchable' => false,
            'closure' => true,
            'wrapper' => function($row) {
                if ($row->user_status == 1)
                    return '<span class="badge badge-md badge-success">' . trans('pos::app.admin.users.users.active') . '</span>';
                else
                    return '<span class="badge badge-md badge-danger">' . trans('pos::app.admin.users.users.inactive') . '</span>';
            }
        ]);

        $this->addColumn([
            'index' => 'user_created_at',
            'label' => trans('pos::app.admin.users.users.date_added'),
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
            'route' => 'admin.pos.users.edit',
            'icon' => 'icon pencil-lg-icon'
        ]);

        $this->addAction([
            'type' => 'Delete',
            'method' => 'POST',
            'route' => 'admin.pos.users.delete',
            'confirm_text' => trans('pos::app.admin.users.users.confirm-users', ['resource' => 'User']),
            'icon' => 'icon trash-icon'
        ]);

        $this->enableAction = true;
    }

    public function prepareMassActions() {
        $this->addMassAction([
            'type' => 'delete',
            'label' => trans('pos::app.admin.users.users.mass-delete'),
            'action' => route('admin.pos.users.massdelete'),
            'method' => 'DELETE'
        ]);

        $this->enableMassAction = true;
    }
}