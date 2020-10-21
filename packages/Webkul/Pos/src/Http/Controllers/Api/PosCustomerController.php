<?php

namespace Webkul\Pos\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Webkul\Customer\Repositories\CustomerGroupRepository as CustomerGroup;
use Webkul\Pos\Repositories\PosCustomerRepository as PosCustomer;
use Webkul\Pos\Http\Resources\Customer as CustomerResource;
use Webkul\Pos\Http\Resources\CustomerGroups as CustomerGroupsResource;

/**
 * PosCustomer controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com>
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosCustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $_config;

    /**
     * customerGroup object
     *
     * @var array
     */
    protected $customerGroup;

    /**
     * posCustomer object
     *
     * @var array
     */
    protected $posCustomer;

    public function __construct(
        PosCustomer $posCustomer,
        CustomerGroup $customerGroup
    )
    {
        $this->middleware('posuser');
        
        $this->_config = request('_config');

        $this->posCustomer = $posCustomer;

        $this->customerGroup = $customerGroup;
    }

    public function saveCustomer()
    {
        $params = request()->all();

        $response = $this->validateCustomerForm($params);

        if (isset($response['status']) && !$response['status']) {
            return response()->json($response);
        } else {

            $params['status'] = $params['is_verified'] =true;
            $params['channel_id'] = core()->getDefaultChannel()->id;

            if (!$params['customer_group_id']) {
                $customerGroup = $this->customerGroup->first();
                $params['customer_group_id'] = $customerGroup->id;
            }

            $params['password'] = bcrypt($params['email']);
            $posCustomer = $this->posCustomer->create($params);

            if ($posCustomer->id) {
                return response()->json([
                    'status' => true,
                    'message' => 'Success: Customer created successfully!',
                    'route'  => 'pos_customer'
                ]);
            }
        }
    }

    public function updateCustomer() {
        $params = request()->all();

        $response = $this->validateCustomerForm($params);

        if (isset($response['status']) && !$response['status']) {
            return response()->json($response);
        } else {

            if (!$params['customer_group_id']) {
                $customerGroup = $this->customerGroup->first();
                $params['customer_group_id'] = $customerGroup->id;
            }

            $posCustomer = $this->posCustomer->findByField('email', $params['customer_email']);

            if ( isset($posCustomer[0]->id) && $posCustomer[0]->id) {
                $posCustomer = $this->posCustomer->update($params, $posCustomer[0]->id);

                if ($posCustomer->id) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Success: Customer modified successfully!',
                        'route'  => 'pos_customer'
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Warning: Customer record not found!',
                ]);
            }
        }
    }

    public function getCustomerGroups() {
        return CustomerGroupsResource::collection($this->customerGroup->all()->where('code', '<>', 'guest'));
    }

    public function getCustomers() {

        $params = request()->input();
        
        if ( auth()->guard('posuser')->check() ) {

            $pos_user = auth()->guard('posuser')->user();
            
            if ( !isset($params['user_id']) || (isset($params['user_id']) && ($pos_user->id != $params['user_id']))) {
                return response()->json([
                    'status'    => false,
                    'message'   => 'Warning: Invalid User.'
                ]);
            }
            
            return CustomerResource::collection($this->posCustomer->getAll());
            
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Invalid User'
            ]);    
        }        
    }

    public function validateCustomerForm($params) {
        if ( auth()->guard('posuser')->check() ) {

            $pos_user = auth()->guard('posuser')->user();

            if ( !isset($params['user_id']) || (isset($params['user_id']) && ($pos_user->id != $params['user_id']))) {
                return [
                    'status' => false,
                    'message' => 'Invalid User'
                ];
            }

            if ( $params['email'] ) {
                $posCustomer = $this->posCustomer->findByField('email', $params['email']);
                
                if ( isset($posCustomer[0]->id) && ( (isset($params['customer_email']) && ($params['customer_email'] != $posCustomer[0]->email)) || !$params['customer_email']) ) {
                    return [
                        'status' => false,
                        'key' => 'already_exist',
                        'message' => 'Warning: This email address is already in used!'
                    ];
                }
            }
           
            if ( !$params['first_name'] ) {
                return [
                    'status' => false,
                    'message' => 'Warning: please provide the value for first_name field!'
                ];
            }
            if ( !$params['last_name'] ) {
                return [
                    'status' => false,
                    'message' => 'Warning: please provide the value for last_name field!'
                ];
            }

            if ( !$params['email'] ) {
                return [
                    'status' => false,
                    'message' => 'Warning: Fill the email field!'
                ];
            }
        } else {
            return [
                'status' => false,
                'message' => 'User Not Login'
            ];
        }
    }
}