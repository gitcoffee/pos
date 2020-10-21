<?php

namespace Webkul\Pos\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Webkul\Pos\Models\PosUser;
use Webkul\Pos\Repositories\PosOutletRepository as PosOutlet;
use Webkul\Pos\Repositories\PosOutletProductRepository as PosOutletProduct;
use Webkul\Category\Repositories\CategoryRepository as Category;
use Webkul\Category\Models\CategoryTranslation;
use Webkul\Pos\Http\Resources\OutletProduct as OutletProductResource;

/**
 * PosProduct controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com>
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $_config;

    /**
     * posOutlet object
     *
     * @var array
     */
    protected $posOutlet;

    /**
     * posOutletProduct object
     *
     * @var array
     */
    protected $posOutletProduct;
    
    protected $category;

    public function __construct(
        PosOutlet $posOutlet,
        Category $category,
        PosOutletProduct $posOutletProduct
    )
    {
        $this->middleware('posuser')->except(['getOutletProducts']);
        
        $this->_config = request('_config');

        $this->posOutlet = $posOutlet;

        $this->category = $category;

        $this->posOutletProduct = $posOutletProduct;
    }

    public function getPosCategories()
    {
        $category_tree = array();

        $locale = request()->get('locale') ?: app()->getLocale();

        if ( auth()->guard('posuser')->check() ) {
            if ($rootCategoryId = core()->getDefaultChannel()->root_category_id) {
                $children_categories = array();
                
                $children_categories = $this->category->getCategoryTree($rootCategoryId);

                $category_tree = $children_categories;
            }
            return response()->json([
                'status'        => true,
                'categories'    => $category_tree
            ]);
        } else {
            return response()->json([
                'status'        => false,
                'categories'    => array()
            ]);
        }
    }

    public function getOutletProducts(Request $request)
    {
        $params = request()->input();
     
        if ( auth()->guard('posuser')->check() ) {

            $pos_user = auth()->guard('posuser')->user();
            
            if ( !isset($params['outlet_id']) || (isset($params['outlet_id']) && ($pos_user->outlet_id != $params['outlet_id']))) {
                return response()->json([
                    'status'    => false,
                    'message'   => 'Invalid Outlet'
                ]);
            }

            return OutletProductResource::collection($this->posOutletProduct->getAll());
        } else {
            return response()->json([
                'status'    => false,
                'message'   => 'Invalid User'
            ]);    
        }        
    }

    public function validateSku()
    {
        $this->validate(request(), [
            'name'  => ['required'],
            'sku'   => ['required', 'unique:products,sku', new \Webkul\Core\Contracts\Validations\Slug]
        ]);

        return response()->json(); 
    }
}