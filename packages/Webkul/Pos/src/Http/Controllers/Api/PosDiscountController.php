<?php

namespace Webkul\Pos\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Webkul\Pos\Models\PosUser;
use Webkul\Pos\Repositories\PosDiscountRepository as PosDiscount;
use Webkul\Pos\Http\Resources\Discount as DiscountResource;

/**
 * PosDiscount controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com>
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosDiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $_config;

    /**
     * posDiscount object
     *
     * @var array
     */
    protected $posDiscount;

    public function __construct(
        PosDiscount $posDiscount
    )
    {
        $this->middleware('posuser')->except(['show', 'checkUserLogin', 'login']);
        
        $this->_config = request('_config');

        $this->posDiscount = $posDiscount;
    }

    public function saveDiscount() {
        $params = request()->all();

        $response = $this->validateDiscountForm($params);

        if (isset($response['status']) && !$response['status']) {
            return response()->json($response);
        } else {

            $params['status'] = true;

            $params['fromprice'] = core()->convertToBasePrice($params['fromprice']);
            $params['toprice'] = core()->convertToBasePrice($params['toprice']);
            if ( $params['type'] != 'percentage' ) {
                $params['value'] = core()->convertToBasePrice($params['value']);
            }

            $posDiscount = $this->posDiscount->create($params);

            if ($posDiscount->id) {
                return response()->json([
                    'status' => true,
                    'message' => 'Success: Discount added successfully!',
                    'route'  => 'pos_setting'
                ]);
            }
        }
    }

    public function updateDiscount()
    {
        $params = request()->all();
        
        $response = $this->validateDiscountForm($params);

        if (isset($response['status']) && !$response['status']) {
            return response()->json($response);
        } else {
            
            $params['fromprice'] = core()->convertToBasePrice($params['fromprice']);
            $params['toprice'] = core()->convertToBasePrice($params['toprice']);
            if ( $params['type'] != 'percentage' ) {
                $params['value'] = core()->convertToBasePrice($params['value']);
            }

            $this->posDiscount->update($params, $params['discount_id']);

            return response()->json([
                'status' => true,
                'message' => 'Success: Discount modified successfully!',
                'route'  => 'pos_setting'
            ]);
        }
    }

    public function removeDiscount()
    {
        $params = request()->all();

        if ( auth()->guard('posuser')->check() ) {

            $pos_user = auth()->guard('posuser')->user();

            if ( !isset($params['user_id']) || (isset($params['user_id']) && ($pos_user->id != $params['user_id']))) {
                return $errors = [
                    'status' => false,
                    'message' => 'Warning: Invalid User!'
                ];
            }

            $posDiscount = $this->posDiscount->find($params['discount_id']);

            if (isset($posDiscount->id) && $posDiscount->id == $params['discount_id']) {
                
                $this->posDiscount->delete($params['discount_id']);

                return response()->json([
                    'status' => true,
                    'message' => 'Success: Discount deleted successfully!',
                    'route'  => 'pos_setting'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Warning: Invalid discount entry!'
                ]);
            }
        }
    }

    public function validateDiscountForm($params) {
        $errors = [];
        if ( auth()->guard('posuser')->check() ) {

            $pos_user = auth()->guard('posuser')->user();

            if ( !isset($params['user_id']) || (isset($params['user_id']) && ($pos_user->id != $params['user_id']))) {
                return $errors = [
                    'status' => false,
                    'message' => 'Warning: Invalid User!'
                ];
            }

            if ( $params['offername'] ) {
                $posDiscount = $this->posDiscount->findByField('offername', $params['offername']);

                if (isset($posDiscount[0]->user_id) && $posDiscount[0]->user_id) {
                    
                    if ($posDiscount[0]->user_id == $params['user_id'] && ($params['discount_id'] != $posDiscount[0]->id)) {
                        return $errors = [
                            'status' => false,
                            'message' => 'Warning: discount with this name already added!'
                        ];
                    }
                }
            }
           
            if ( !$params['fromprice'] ) {
                return $errors = [
                    'status' => false,
                    'message' => 'Warning: please provide the value for from-price field!'
                ];
            }
            if ( !$params['toprice'] ) {
                return $errors = [
                    'status' => false,
                    'message' => 'Warning: please provide the value for to-price field!'
                ];
            }

            if ( !$params['value'] ) {
                return $errors = [
                    'status' => false,
                    'message' => 'Warning: Fill the discount value field!'
                ];
            }

            if ( $params['fromprice'] && $params['toprice'] && ($params['fromprice'] > $params['toprice']) ) {
                return $errors = [
                    'status' => false,
                    'message' => 'Warning: from-price can not be greater than to-price!'
                ];
            }
        } else {
            return $errors = [
                'status' => false,
                'message' => 'Warning: User Not Login!'
            ];
        }
    }

    public function getUserDiscounts() {

        $params = request()->input();
        
        if ( auth()->guard('posuser')->check() ) {

            $pos_user = auth()->guard('posuser')->user();
            
            if ( !isset($params['user_id']) || (isset($params['user_id']) && ($pos_user->id != $params['user_id']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Warning: Invalid User!'
                ]);
            }

            return DiscountResource::collection($this->posDiscount->getAll());
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Warning: Invalid User!'
            ]);    
        }        
    }    
}