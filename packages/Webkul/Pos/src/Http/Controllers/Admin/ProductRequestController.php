<?php

namespace Webkul\Pos\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Webkul\Pos\Repositories\PosOutletRepository as PosOutlet;
use Webkul\Pos\Repositories\PosUserRepository as PosUser;
use Webkul\Pos\Repositories\PosProductRequestRepository as PosProductRequest;
use Webkul\Product\Repositories\ProductFlatRepository as ProductFlat;
use Webkul\Product\Repositories\ProductInventoryRepository as ProductInventory;
use Webkul\Inventory\Repositories\InventorySourceRepository as InventorySource;

/**
 * ProductRequest controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com>
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class ProductRequestController extends Controller
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
     * PosProductRequest object
     *
     * @var array
     */
    protected $posProductRequest;

    /**
     * PosProductRequest object
     *
     * @var array
     */
    protected $productFlat;

    /**
     * ProductInventory object
     *
     * @var array
     */
    protected $productInventory;

    /**
     * InventorySource object
     *
     * @var array
     */
    protected $inventorySource;

    

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        PosOutlet $posOutlet,
        PosUser $posUser,
        PosProductRequest $posProductRequest,
        ProductFlat $productFlat,
        ProductInventory $productInventory,
        InventorySource $inventorySource
    )
    {
        $this->_config = request('_config');

        $this->posOutlet = $posOutlet;

        $this->posUser = $posUser;

        $this->posProductRequest = $posProductRequest;

        $this->productFlat = $productFlat;

        $this->productInventory = $productInventory;

        $this->inventorySource = $inventorySource;
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function view($id)
    {
        $posProductRequest = $this->posProductRequest->find($id);

        if ( isset($posProductRequest->id) && $posProductRequest->id == $id ) {

            $productFlat = $this->productFlat->findWhere(['product_id' => $posProductRequest->product_id, 'locale' => config('app.locale')])->first();

            $posUser = $this->posUser->find($posProductRequest->user_id);

            $posOutlet = $this->posOutlet->find($posUser->outlet_id);

            $posOutlet->inventory_source_name = $this->inventorySource->find($posOutlet->inventory_source_id)->name;

            return view($this->_config['view'], compact('posProductRequest', 'productFlat', 'posUser', 'posOutlet'));
        } else {
            return view('pos::admin.requests.index');
        }
    }

    /**
     * Update the specified resource in storage.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $data = request()->all();
        
        if ( $data['_method'] != 'PUT' ) {
            session()->flash('warning', 'Invalid Request');
        } else {
            if ( isset($data['update-options']) ) {

                if ($this->updateInventory($data, $id)) {
                    session()->flash('success', trans('pos::app.admin.requests.response.success_updated'));
                } else {
                    session()->flash('warning', trans('pos::app.admin.requests.response.error_updated'));
                }
            }
        }

        return redirect()->route($this->_config['redirect']);
    }

    public function massupdate()
    {
        $data = request()->all();

        $requestIds = explode(',', request()->input('indexes'));

        $records_count = 0;

        if (empty($requestIds)) {
            session()->flash('error', trans('pos::app.admin.requests.response.no_selection'));
        } else {
            if (request()->input('massaction-type') == 'update') {

                foreach ($requestIds as $request_id) {
                    if ($this->updateInventory($data, $request_id)) {
                        $records_count += 1;
                    }
                }

                if ($records_count > 0) {
                    session()->flash('success', trans('pos::app.admin.requests.response.success_records_updated', [ 'count' => $records_count ]));
                } else {
                    session()->flash('warning', trans('pos::app.admin.requests.response.error_records_updated', [ 'count' => $records_count ]));
                }
            }
        }
        
        return redirect()->route('admin.pos.requests.index');
    }

    public function updateInventory($data, $request_id)
    {
        $records_count = 0;

        $posProductRequest = $this->posProductRequest->find($request_id);

        if ( isset($posProductRequest->id) && ($posProductRequest->id == $request_id) ) {

            $posUser = $this->posUser->find($posProductRequest->user_id);

            if ( isset($posUser->id) && ($posUser->id == $posProductRequest->user_id) ) {

                $posOutlet = $this->posOutlet->find($posUser->outlet_id);

                if ( isset($posOutlet->id) && $posOutlet->id == $posUser->outlet_id ) {

                    if ( isset($data['update-options'])) {
                        $productFlat = $this->productFlat->findByField('product_id', $posProductRequest->product_id);

                        $productFlat = $productFlat->first();

                        if ( isset($productFlat->product_id) && ($productFlat->product_id == $posProductRequest->product_id) ) {
                            
                            $productInventory = $this->productInventory->findWhere(['product_id' => $posProductRequest->product_id, 'inventory_source_id' => $posOutlet->inventory_source_id]);

                            $productInventory = $productInventory->first();

                            if ( isset($productInventory->product_id) && ($productInventory->product_id == $posProductRequest->product_id) && ($productInventory->inventory_source_id == $posOutlet->inventory_source_id) ) {

                                if ( $data['update-options'] == 1 && $posProductRequest->request_status != 1 ) { // complete
                                    $productInventory->qty += $posProductRequest->requested_quantity;

                                    $this->productInventory->update(['qty' => $productInventory->qty], $productInventory->id);

                                    $this->posProductRequest->update(['request_status' => '1'], $posProductRequest->id);

                                    $records_count = 1;
                                }

                                if ( $data['update-options'] == 2 &&$posProductRequest->request_status != 1 ) { // decline
                                    $this->posProductRequest->update(['request_status' => '2'], $posProductRequest->id);

                                    $records_count = 1;
                                }

                                if ( $data['update-options'] == 0 && $posProductRequest->request_status == 2 ) { // pending
                                    $this->posProductRequest->update(['request_status' => '0'], $posProductRequest->id);

                                    $records_count = 1;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $records_count;
    }
}