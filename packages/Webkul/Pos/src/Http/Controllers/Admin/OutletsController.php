<?php

namespace Webkul\Pos\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Webkul\Product\Repositories\ProductRepository as Product;
use Webkul\Pos\Repositories\PosOutletRepository as PosOutlet;
use Webkul\Pos\Repositories\PosOutletProductRepository as PosOutletProduct;
use Webkul\Pos\Repositories\PosOrderRepository;
use Webkul\Product\Repositories\ProductFlatRepository as ProductFlat;

/**
 * Outlets controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com>
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class OutletsController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * Product object
     *
     * @var array
     */
    protected $product;

    /**
     * PosOutletRepository object
     *
     * @var array
     */
    protected $posOutlet;
    
    /**
     * PosOutletProductRepository object
     *
     * @var array
     */
    protected $posOutletProduct;
    
    /**
     * PosOrderRepository object
     *
     * @var array
     */
    protected $posOrderRepository;
    
    /**
     * PosOutletProductRepository object
     *
     * @var array
     */
    protected $productFlat;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        Product $product,
        ProductFlat $productFlat,
        PosOutlet $posOutlet,
        PosOrderRepository $posOrderRepository,
        PosOutletProduct $posOutletProduct
        )
    {
        $this->_config = request('_config');
        
        $this->product = $product;

        $this->productFlat = $productFlat;
        
        $this->posOutlet = $posOutlet;

        $this->posOrderRepository = $posOrderRepository;

        $this->posOutletProduct = $posOutletProduct;
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function outletProducts()
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
        $inventorySource = core()->getCurrentChannel()->inventory_sources()->where('status', 1)->get();
        
        return view($this->_config['view'], compact('inventorySource'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $data = request()->all();

        $posOutlet = $this->posOutlet->findByField('inventory_source_id', $data['inventory_source_id']);

        if ( !isset($posOutlet[0]->id) ) {
            $this->posOutlet->create($data);
            
            session()->flash('success', trans('pos::app.admin.response.outlet-success-create', ['name' => 'Outlet']));
        } else {
            session()->flash('warning', trans('pos::app.admin.response.outlet-warning-create', ['name' => 'Outlet']));
            
            $this->_config['redirect'] = 'admin.pos.outlets.create';
        }

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
        $outlet = $this->posOutlet->find($id);

        $inventorySource = core()->getCurrentChannel()->inventory_sources()->where('status', 1)->get();

        return view($this->_config['view'], compact('outlet', 'inventorySource'));
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

        $posOutlet = $this->posOutlet->findByField('inventory_source_id', $data['inventory_source_id'])->first();

        if ( !isset($posOutlet->id) ) {
            $this->posOutlet->update($data, $id);
        } else {
            if ( isset($posOutlet->id) && $posOutlet->id == $id) {
                $this->posOutlet->update($data, $id);
            } else {
                session()->flash('warning', trans('pos::app.admin.response.outlet-warning-create', ['name' => 'Outlet']));
            
                $this->_config['redirect'] = 'admin.pos.outlets.edit';
                return redirect()->route($this->_config['redirect'], $id);
            }
        }

        session()->flash('success', trans('pos::app.admin.response.outlet-success-update', ['name' => 'Outlet']));

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
        if ($this->posOutlet->count() == 0) {
            session()->flash('error', trans('pos::app.admin.response.outlet-error-atleast'));
        } else {
            try {
                $this->posOrderRepository->deleteWhere(['outlet_id' => $id]);

                $this->posOutlet->delete($id);

                session()->flash('success', trans('pos::app.admin.response.outlet-success-delete'));

                return response()->json(['message' => true], 200);
            } catch(\Exception $e) {
                session()->flash('error', trans('admin::app.response.delete-failed', ['name' => 'Company']));
            }
    
            return response()->json(['message' => false], 400);
        }

        return response()->json(['message' => false], 400);
    }

    /**
     * Mass Delete the outlets
     *
     * @return response
     */
    public function massDestroy()
    {
        $suppressFlash = false;

        if (request()->isMethod('post')) {
            $OutletIds = explode(',', request()->input('indexes'));

            foreach ($OutletIds as $outletId) {
                try {
                    $this->posOrderRepository->deleteWhere(['outlet_id' => $id]);

                    $this->posOutlet->delete($outletId);
                } catch (\Exception $e) {
                    $suppressFlash = true;

                    continue;
                }
            }

            if (! $suppressFlash)
                session()->flash('success', trans('pos::app.admin.response.outlet-success-massdelete'));
            else
                session()->flash('info', trans('admin::app.datagrid.mass-ops.partial-action', ['resource' => 'Pos Outlets']));

            return redirect()->back();
        } else {
            session()->flash('error', trans('admin::app.datagrid.mass-ops.method-error'));

            return redirect()->back();
        }
    }

    public function assignProduct()
    {
        $posOutlet = $this->posOutlet->find(request()->id);

        if ( isset($posOutlet['id']) && $posOutlet['id'] == request()->id) {
            
            return view($this->_config['view']);    
        }

        return redirect()->route($this->_config['redirect']);
    }

    public function assignSave()
    {
        $data = request()->all();

        $previousUrl = explode('/', url()->previous());
        $outletId = end($previousUrl);

        if ( !isset($data['indexes']) && !isset($data['status']) ) {
            return response()->json([
                'status' => false,
                'message' => trans('pos::app.admin.response.outlet-update-warning')
            ]);
        } else {
            $outlet = $this->posOutlet->find($outletId);

            if ( isset($outlet->id) && ((int) $outlet->id == (int) $outletId) ) {
                $resultUpdate = $this->posOutletProduct->singleAssignToOutlet($data, $outlet->id);
                
                if ($resultUpdate) {
                    return response()->json([
                        'status' => true,
                        'message' => trans('pos::app.admin.response.outlet-update-success')
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => trans('pos::app.admin.response.outlet-warning-invalid-outlet')
                ]);
            }
        }        
    }

    public function massAssign()
    {
        $data = request()->all();

        $productIds = explode(',', request()->input('indexes'));

        if (empty($productIds)) {
            session()->flash('error', trans('pos::app.admin.response.outlet-assign-warning'));
        } else {
            $previousUrl = explode('/', url()->previous());

            $outletId = end($previousUrl);

            $outlet = $this->posOutlet->find($outletId);

            if ( isset($outlet->id) && $outlet->id) {
                $this->posOutletProduct->assignToOutlet($data, $outlet->id);
            }

            session()->flash('success', trans('pos::app.admin.response.outlet-assign-success'));
            
            return redirect()->route('admin.pos.outlets.assign', ['id' => $outlet->id]);
        }
        
        return redirect()->route('admin.pos.outlets.index');
    }

    public function massUnassign()
    {
        $productIds = explode(',', request()->input('indexes'));

        $previousUrl = explode('/', url()->previous());

        $outletId = end($previousUrl);

        if ($productIds && $outletId) {
            foreach ($productIds as $key => $product_id) {
                $this->posOutletProduct->deleteWhere(['product_id' => $product_id, 'outlet_id' => $outletId]);
            }

            session()->flash('success', trans('pos::app.admin.response.outlet-unassign-success'));
            
            return redirect()->route('admin.pos.outlets.assign', ['id' => $outletId]);
        }
        return redirect()->route('admin.pos.outlets.index');
    }
}