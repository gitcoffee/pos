<?php

namespace Webkul\Pos\Http\Controllers\Admin;

use Webkul\Pos\Repositories\PosOutletRepository as PosOutlet;
use Webkul\Pos\Repositories\PosTableBookingRepository;

/**
 * BookedHistory controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com>
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class BookedHistoryController extends Controller
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
     * PosTableBookingRepository object
     *
     * @var array
     */
    protected $posTableBookingRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        PosOutlet $posOutlet,
        PosTableBookingRepository $posTableBookingRepository
    )   {
        $this->_config = request('_config');

        $this->posOutlet = $posOutlet;

        $this->posTableBookingRepository = $posTableBookingRepository;
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->posTableBookingRepository->count() == 0) {
            session()->flash('error', trans('pos::app.admin.response.user-error-atleast'));
        } else {

            $this->posTableBookingRepository->delete($id);

            session()->flash('success', trans('pos::app.admin.restaurants.tables.success-delete-booking'));
        }

        return redirect()->back();
    }

}