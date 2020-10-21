<?php

namespace Webkul\Pos\Http\Controllers\Admin;

use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Webkul\Pos\Repositories\PosTableRepository;
use Webkul\Pos\Repositories\PosUserRepository;

/**
 * Restautant Tables controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com>
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class TablesController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * PosTableRepository object
     *
     * @var array
     */
    protected $posTableRepository;

    /**
     * PosUserRepository object
     *
     * @var array
     */
    protected $posUserRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        PosTableRepository $posTableRepository,
        PosUserRepository $posUserRepository
    )
    {
        $this->_config = request('_config');

        $this->posTableRepository = $posTableRepository;

        $this->posUserRepository = $posUserRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view($this->_config['view']);
    }

    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function create()
    {
        $posUsers = $this->posUserRepository->all();

        $table_types = explode(",", core()->getConfigData('pos.restaurant.general.table_shape'));
        
        return view($this->_config['view'], compact('posUsers', 'table_types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $data = request()->all();

        $this->validate(request(), [
            'type'      => 'required|string',
            'name'      => 'required|max:50',
            'no_of_seat' => 'required|numeric',
            'agent_id'  => 'required|numeric',
            'position'  => [
                'required',
                Rule::unique('pos_restaurant_tables')->where(function ($query) use($data) {
                    return $query->where('position', $data['position'])
                    ->where('agent_id', $data['agent_id']);
                })
            ]
        ]);
        
        $table = $this->posTableRepository->create($data);

        session()->flash('success', trans('pos::app.admin.restaurants.tables.success-create-table'));

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $posTable = $this->posTableRepository->findOrFail($id);

        $posUsers = $this->posUserRepository->all();

        $table_types = explode(",", core()->getConfigData('pos.restaurant.general.table_shape'));
        
        return view($this->_config['view'], compact('posTable', 'posUsers', 'table_types'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $data = request()->all();

        $this->validate(request(), [
            'type'          => 'required|string',
            'name'          => 'required|string',
            'no_of_seat'    => 'required|numeric',
            'agent_id'      => 'required',
            'position'      => [
                'required',
                Rule::unique('pos_restaurant_tables')->where(function ($query) use($data, $id) {
                    return $query->where('position', $data['position'])
                    ->where('agent_id', $data['agent_id'])
                    ->where('id', '!=',  $id);
                })
            ],
        ]);

        $posTable = $this->posTableRepository->update(request()->all(), $id);

        session()->flash('success', trans('pos::app.admin.restaurants.tables.success-update-table'));

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->posTableRepository->count() == 0) {
            session()->flash('error', trans('pos::app.admin.response.user-error-atleast'));
        } else {

            $this->posTableRepository->delete($id);

            session()->flash('success', trans('pos::app.admin.restaurants.tables.success-delete-table'));
        }

        return redirect()->back();
    }

    /**
     * Mass Delete the outlets
     *
     * @return response
     */
    public function massDestroy()
    {
        $tablesIds = explode(',', request()->input('indexes'));

        foreach ($tablesIds as $tableId) {
            $this->posTableRepository->delete($tableId);
        }

        session()->flash('success', trans('pos::app.admin.restaurants.tables.success-mass-delete'));

        return redirect()->route($this->_config['redirect']);
    }
}