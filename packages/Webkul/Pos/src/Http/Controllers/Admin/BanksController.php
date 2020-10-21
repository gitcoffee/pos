<?php

namespace Webkul\Pos\Http\Controllers\Admin;

use Illuminate\Http\Response;
use Webkul\Pos\Repositories\PosUserRepository;
use Webkul\Pos\Repositories\PosBankRepository;

/**
 * Banks controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com>
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class BanksController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * PosUserRepository object
     *
     * @var array
     */
    protected $posUserRepository;

    /**
     * PosBankRepository object
     *
     * @var array
     */
     protected $posBankRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        PosUserRepository $posUserRepository,
        PosBankRepository $posBankRepository
    )
    {
        $this->_config = request('_config');

        $this->posUserRepository = $posUserRepository;

        $this->posBankRepository = $posBankRepository;
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
        $agents = $this->posUserRepository->all();

        return view($this->_config['view'], compact('agents'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'name' => 'required|max:50',
            'address' => 'required|max:250',
            'agent_id' => 'required',
        ]);

        $data = request()->all();

        $this->posBankRepository->create($data);

        session()->flash('success', trans('pos::app.admin.response.bank-success-create'));

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
        $posBank = $this->posBankRepository->find($id);

        $agents = $this->posUserRepository->all();

        return view($this->_config['view'], compact('posBank', 'agents'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $this->validate(request(), [
            'name' => 'required|max:50',
            'address' => 'required|max:250',
            'agent_id' => 'required',
        ]);

        $data = request()->all();
        
        $this->posBankRepository->update($data, $id);

        session()->flash('success', trans('pos::app.admin.response.bank-success-update'));

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
        if ($this->posBankRepository->count() == 0) {
            session()->flash('error', trans('pos::app.admin.response.bank-error-atleast'));
        } else {

            $this->posBankRepository->delete($id);

            session()->flash('success', trans('pos::app.admin.response.bank-success-delete'));
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
        $bankIds = explode(',', request()->input('indexes'));

        foreach ($bankIds as $bankId) {
            $this->posBankRepository->delete($bankId);
        }

        session()->flash('success', trans('pos::app.admin.response.bank-success-massdelete'));

        return redirect()->route($this->_config['redirect']);
    }
}