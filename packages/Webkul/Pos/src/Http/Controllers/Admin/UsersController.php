<?php

namespace Webkul\Pos\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Webkul\Pos\Http\Requests\Admin\UserForm;
use Webkul\Pos\Repositories\PosOutletRepository as PosOutlet;
use Webkul\Pos\Repositories\PosUserRepository as PosUser;

/**
 * Users controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com>
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class UsersController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * PosOutletRepository object
     *
     * @var array
     */
    protected $posOutlet;

    /**
     * PosUserRepository object
     *
     * @var array
     */
    protected $posUser;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        PosOutlet $posOutlet,
        PosUser $posUser
    )
    {
        $this->_config = request('_config');

        $this->posOutlet = $posOutlet;

        $this->posUser = $posUser;
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
        $outlets = $this->posOutlet->all();

        return view($this->_config['view'], compact('outlets'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Webkul\Pos\Http\Requests\Admin\UserForm $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserForm $request)
    {
        $data = request()->all();
        
        $data['changed'] = 0;
        if (isset($data['password']) && $data['password'])
            $data['password'] = bcrypt($data['password']);

        $this->posUser->create($data);

        session()->flash('success', trans('pos::app.admin.response.user-success-create'));

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
        $posUser = $this->posUser->find($id);

        if (isset($posUser['outlet_id']) && $posUser['outlet_id']) 
            $posUser['outlet_name'] = $this->posOutlet->find($posUser['outlet_id'], ['name'])->name;
        else
            $posUser['outlet_name'] = '';

        $posUser['avatar'] = '';
        if ($posUser->image) {
            $posUser['avatar'] = $this->posUser->getImageUrl($posUser);
        }
        
        $outlets = $this->posOutlet->all();

        return view($this->_config['view'], compact('posUser', 'outlets'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Webkul\Pos\Http\Requests\Admin\UserForm $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserForm $request, $id)
    {
        $data = request()->all();
        
        if (! $data['password'])
            unset($data['password']);
        else
            $data['password'] = bcrypt($data['password']);

        $data['image'] = '';
        if (request()->input('avatar')) {
            $image = array_values(request()->input('avatar'));

            if (isset($image[0]) && $image[0])
                $data['image'] = $image[0];
        }

        $data['changed'] = 1;

        $posUser = $this->posUser->update($data, $id);

        session()->flash('success', trans('pos::app.admin.response.user-success-update'));

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
        if ($this->posUser->count() == 0) {
            session()->flash('error', trans('pos::app.admin.response.user-error-atleast'));
        } else {

            $this->posUser->delete($id);

            session()->flash('success', trans('pos::app.admin.response.outlet-success-delete'));
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
        $UsersIds = explode(',', request()->input('indexes'));

        foreach ($UsersIds as $userId) {
            $this->posUser->delete($userId);
        }

        session()->flash('success', trans('pos::app.admin.response.user-success-massdelete'));

        return redirect()->route($this->_config['redirect']);
    }
}