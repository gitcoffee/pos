<?php

namespace Webkul\Pos\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Webkul\Pos\Repositories\PosProductRequestRepository as PosProductRequest;
use Webkul\Pos\Repositories\PosOutletProductRepository as PosOutletProduct;
use Webkul\Pos\Http\Resources\ProductRequest as ProductRequestResource;

/**
 * PosProductRequest controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com>
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosProductRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $_config;

    /**
     * posProductRequest object
     *
     * @var array
     */
    protected $posProductRequest;

    /**
     * product object
     *
     * @var array
     */
    protected $posOutletProduct;

    public function __construct(
        PosProductRequest $posProductRequest,
        PosOutletProduct $posOutletProduct
    )
    {
        $this->middleware('posuser');
        
        $this->_config = request('_config');

        $this->posProductRequest = $posProductRequest;

        $this->posOutletProduct = $posOutletProduct;
    }

    public function saveRequest() {
        $params = request()->all();

        $response = $this->validateRequestForm($params);

        if (isset($response['status']) && !$response['status']) {
            return response()->json($response);
        } else {

            $posProductRequest = $this->posProductRequest->create($params);

            if ($posProductRequest->id) {
                return response()->json([
                    'status' => true,
                    'message' => 'Success: Request added successfully!',
                    'route'  => 'pos_product_low_stock'
                ]);
            }
        }
    }

    public function updateRequest()
    {
        $params = request()->all();

        $response = $this->validateRequestForm($params);
        
        if (isset($response['status']) && !$response['status']) {
            return response()->json($response);
        } else {
            $posProductRequest = $this->posProductRequest->find($params['request_id']);

            if ( isset($posProductRequest->id) && $posProductRequest->id == $params['request_id']) {
                if ( ($posProductRequest->product_id == $params['product_id']) && ($posProductRequest->user_id == $params['user_id'])) {
                    $this->posProductRequest->update($params, $params['request_id']);

                    return response()->json([
                        'status' => true,
                        'message' => 'Success: Request modified successfully!',
                        'route'  => 'pos_product_low_stock'
                    ]);
                }
            }
        }
    }

    public function removeRequest()
    {
        $params = request()->all();

        if ( auth()->guard('posuser')->check() ) {

            $pos_user = auth()->guard('posuser')->user();

            if ( !isset($params['user_id']) || (isset($params['user_id']) && ($pos_user->id != $params['user_id']))) {
                return [
                    'status' => false,
                    'message' => 'Invalid User'
                ];
            }

            $posProductRequest = $this->posProductRequest->find($params['request_id']);

            if (isset($posProductRequest->id) && $posProductRequest->id == $params['request_id']) {
                
                $this->posProductRequest->delete($params['request_id']);

                return response()->json([
                    'status' => true,
                    'message' => 'Success: Request deleted successfully!',
                    'route'  => 'pos_product_low_stock'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Warning: Invalid discount entry!'
                ]);
            }
        }
    }

    public function validateRequestForm($params) {
        if ( auth()->guard('posuser')->check() ) {

            $pos_user = auth()->guard('posuser')->user();

            if ( !isset($params['user_id']) || (isset($params['user_id']) && ($pos_user->id != $params['user_id']))) {
                return $errors = [
                    'status' => false,
                    'message' => 'Invalid User'
                ];
            }

            if ( !$params['requested_quantity'] ) {
                return $errors = [
                    'status' => false,
                    'message' => 'Warning: please provide the value for requested quantity field!'
                ];
            }

            if ( !$params['product_id'] ) {
                return $errors = [
                    'status' => false,
                    'message' => 'Warning: Invalid product selection!'
                ];
            }

            if ( !$params['comment'] || (strlen($params['comment']) < 5 || strlen($params['comment']) >250) ) {
                return $errors = [
                    'status' => false,
                    'message' => 'Warning: Comment must be between 5 and 250 characters!'
                ];
            }

            if ( $params['product_id'] ) {
                $posOutletProduct = $this->posOutletProduct->findWhere(['product_id' => $params['product_id'], 'outlet_id' => $pos_user['outlet_id'], 'status' => true]);

                if (isset($posOutletProduct[0]->product_id) && $posOutletProduct[0]->product_id) {
                    
                    if ($posOutletProduct[0]->product_id != $params['product_id']) {
                        return $errors = [
                            'status' => false,
                            'message' => 'Warning: Invalid product selection!'
                        ];
                    }
                }
            }
        } else {
            return $errors = [
                'status' => false,
                'message' => 'User Not Login'
            ];
        }
    }

    public function getLowStockRequestedProducts() {

        $params = request()->input();
        
        if ( auth()->guard('posuser')->check() ) {

            $pos_user = auth()->guard('posuser')->user();
            
            if ( !isset($params['user_id']) || (isset($params['user_id']) && ($pos_user->id != $params['user_id']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid User'
                ]);
            }

            return ProductRequestResource::collection($this->posProductRequest->getAll());
            
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Invalid User'
            ]);    
        }        
    }

    public function sendRequest() {
        $params = request()->all();

        if ( auth()->guard('posuser')->check() ) {

            $pos_user = auth()->guard('posuser')->user();
            
            if ( !isset($params['user_id']) || (isset($params['user_id']) && ($pos_user->id != $params['user_id']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid User'
                ]);
            }

            if (isset($params['requested_list']) && $params['requested_list']) {
                $total_records = count($params['requested_list']);
                $success_records = 0;
                foreach ($params['requested_list'] as $requested_product) {
                    
                    $posProductRequest = $this->posProductRequest->findWhere(['id' => $requested_product['id'], 'product_id' => $requested_product['product_id'], 'user_id' => $params['user_id']]);

                    if (isset($posProductRequest[0]->id) && ($posProductRequest[0]->id == $requested_product['id'])) {
                        $this->posProductRequest->update(['send_status' => 1], $requested_product['id']);
                        $success_records += 1;
                    }
                }
                if ( $success_records == $total_records) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Success: All the request send successfully!',
                        'route' => 'pos_product_low_stock'
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Warning: There is no product added for the Quantity Request!'
                ]);
            }
        }
    }
}