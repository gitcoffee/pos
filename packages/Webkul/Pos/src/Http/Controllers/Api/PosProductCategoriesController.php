<?php

namespace Webkul\Pos\Http\Controllers\Api;

use Webkul\Pos\Repositories\PosProductCategoriesRepository as PosProductCategories;
use Webkul\Pos\Http\Resources\ProductCategories as ProductCategoriesResource;

/**
 * PosProductCategories controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com>
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosProductCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $_config;

    /**
     * posProductCategories object
     *
     * @var array
     */
    protected $posProductCategories;

    public function __construct(
        PosProductCategories $posProductCategories
    )
    {
        $this->middleware('posuser')->except(['show', 'checkUserLogin', 'login']);
        
        $this->_config = request('_config');

        $this->posProductCategories = $posProductCategories;
    }

    public function getPosProductCategories()
    {
        $params = request()->input();

        if ( auth()->guard('posuser')->check() ) {

            $pos_user = auth()->guard('posuser')->user();
            
            if ( !isset($params['outlet_id']) || (isset($params['outlet_id']) && ($pos_user->outlet_id != $params['outlet_id']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid Outlet'
                ]);
            }
            
            return ProductCategoriesResource::collection($this->posProductCategories->getAll([]));
        } else {
            return response()->json([
                'status' => false,
                'product_categories' => []
            ]);
        }
    }

}