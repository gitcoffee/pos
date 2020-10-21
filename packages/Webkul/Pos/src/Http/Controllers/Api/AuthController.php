<?php

namespace Webkul\Pos\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Webkul\Pos\Repositories\PosOutletRepository as PosOutlet;
use Webkul\Pos\Repositories\PosUserRepository as PosOutletUser;
use Webkul\Pos\Repositories\PosDrawerRepository as PosDrawer;
use Webkul\Core\Repositories\CountryStateRepository as CountryState;


/**
 * Auth controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com>
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class AuthController extends Controller
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
     * PosUserRepository object
     *
     * @var array
     */
    protected $posOutletUser;

    /**
     * PosDrawerRepository object
     *
     * @var array
     */
    protected $posDrawer;

    /**
     * CountryState Repository object
     *
     * @var array
     */
    protected $countryState;

    public function __construct(
        PosOutlet $posOutlet,
        PosOutletUser $posOutletUser,
        PosDrawer $posDrawer,
        CountryState $countryState
    )
    {
        $this->middleware('posuser')->except(['show', 'checkUserLogin', 'login']);
        
        $this->_config = request('_config');

        $this->posOutlet = $posOutlet;

        $this->posOutletUser = $posOutletUser;

        $this->posDrawer = $posDrawer;

        $this->countryState = $countryState;
    }

    public function show()
    {
        if ( !core()->getConfigData('pos.configuration.general.status') ) {
            session()->flash('warning', trans('pos::shop.home.enable-pos-status'));

            return redirect()->route('shop.home.index');
        }

        $base_dir_url = '/';
    
        $explode_url = explode("://", config('app.url'));

        if (count($explode_url) > 1 && isset($explode_url[1])) {
            $explode_remain = explode('/', $explode_url[1]);
            
            if ( count($explode_remain) > 1) {
                $url_shift = array_slice($explode_remain, 1);
                foreach ($url_shift as $dir_name) {
                    if ($dir_name) {
                        $base_dir_url .= $dir_name . '/';
                    }
                }
            }
        }

        $csrfToken = csrf_token();

        if ( auth()->guard('posuser')->check() ) {
            $user = auth()->guard('posuser')->user();
            
            $posDrawer = $this->posDrawer->getDrawerRecords(['user_id' => $user->id, 'outlet_id' => $user->outlet_id, 'today_entry' => true])->first();

            if ( !$posDrawer || (isset($user->changed) && $user->changed) ) {
                if ( isset($user->changed) && $user->changed ) {
                    $user->update(['changed' => 0]);

                    auth()->guard('posuser')->logout();
                }
                //auth()->guard('posuser')->logout();
            }
        }
        
        return view($this->_config['view'], ['base_dir_url' => $base_dir_url, 'csrfToken' => $csrfToken]);
    }

    public function checkUserLogin()
    {
        if (! auth()->guard('posuser')->check() ) {
            auth()->guard('posuser')->logout();

            return response()->json([
                'status' => false,
                'route' => 'pos_login'
            ]);
        } else {
            return response()->json([
                'status' => true,
                'route' => 'pos_home'
            ]);
        }
    }

    public function login(Request $request)
    {
        $data = request()->all();
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $params = [
            'username' => $data['username'],
            'password' => $data['password']
        ];

        if ( isset($data['pos_saas']) && $data['pos_saas'] ) {
            $company = \Company::getCurrent();
            if ( isset($company->id)) {
                $params['company_id'] = $company->id;
            }
        }

        if (! auth()->guard('posuser')->attempt($params, request()->input('remember') ? true : false )) {
            return response()->json([
                'status' => false,
                'message' => trans('pos::shop.login.user.login-form.invalid-creds')
            ]);
        }

        if (auth()->guard('posuser')->user()->status == 0) {
            auth()->guard('posuser')->logout();

            return response()->json([
                'status' => false,
                'message' => trans('pos::shop.login.user.login-form.unauth-user')
            ]);
        }
        
        $user_id = auth()->guard('posuser')->user()->id;
        
        $posOutletUser = $this->posOutletUser->find($user_id);
        
        if (isset($posOutletUser->id) && ($posOutletUser->id == $user_id) ) {
            $posOutlet = $this->posOutlet->find($posOutletUser->outlet_id);
            
            $data = [];
            $data = auth()->guard('posuser')->user();
            $data['img_path'] = url('storage/' . auth()->guard('posuser')->user()->image);
            $data['inventory_source_id'] = $posOutlet->inventory_source_id;

            $countryState = $this->countryState->findWhere(['country_code' => $posOutlet->country, 'code' => $posOutlet->state])->first();

            $data['outlet_address'] = [
                'address1' => $posOutlet->address,
                'address2' => '',
                'country' => $posOutlet->country,
                'country_name' => core()->country_name($posOutlet->country),
                'state' => $posOutlet->state,
                'state_name' => isset($countryState->default_name) ? $countryState->default_name : $posOutlet->state,
                'postcode' => $posOutlet->postcode,
            ];

            // get the drawer entry for the today
            return response()->json([
                'status' => true,
                'route' => 'pos_home',
                'message' => 'Success: You have login successfully!',
                'user_data' => $data,
                'drawer_data' => $this->getDrawer(['id' => $user_id, 'outlet_id' => $posOutletUser->outlet_id]),
            ]);
        } else {
            auth()->guard('posuser')->logout();

            return response()->json([
                'status' => false,
                'route' => 'pos_login',
                'user_data' => array()
            ]);
        }
    }

    public function user()
    {
        if (auth()->guard('posuser')->check()) {
            $user_id = auth()->guard('posuser')->user()->id;
            
            $posOutletUser = $this->posOutletUser->find($user_id);

            if ($posOutletUser->id && ($posOutletUser->id == $user_id) ) {
                $data = array();
                $data = auth()->guard('posuser')->user();
                $data['img_path'] = url('storage/' . auth()->guard('posuser')->user()->image);
                $data['famount'] = core()->currency($data['amount']);
                $posOutlet = $this->posOutlet->find($posOutletUser->outlet_id);
                $data['inventory_source_id'] = $posOutlet->inventory_source_id;

                $countryState = $this->countryState->findWhere(['country_code' => $posOutlet->country, 'code' => $posOutlet->state])->first();
                
                $data['outlet_address'] = [
                    'address1' => $posOutlet->address,
                    'address2' => '',
                    'country' => $posOutlet->country,
                    'country_name' => core()->country_name($posOutlet->country),
                    'state' => $posOutlet->state,
                    'state_name' => isset($countryState->default_name) ? $countryState->default_name : $posOutlet->state,
                    'postcode' => $posOutlet->postcode,
                ];
                
                return response()->json([
                    'status' => true,
                    'route' => 'pos_home',
                    'user_data' => $data,
                    'drawer_data' => $this->getDrawer(['id' => $user_id, 'outlet_id' => $posOutletUser->outlet_id]),
                ]);
            } else {
                auth()->guard('posuser')->logout();

                return response()->json([
                    'status' => false,
                    'route' => 'pos_login',
                    'user_data' => array()
                ]);
            }
        } else {
            auth()->guard('posuser')->logout();

            return response()->json([
                'status' => false,
                'route' => 'pos_login',
                'user_data' => array()
            ]);
        }
    }

    public function destroy()
    {
        $params = request()->all();

        if (auth()->guard('posuser')->check()) {
            $user_id = auth()->guard('posuser')->user()->id;

            if ( !isset($params['user_id']) || (isset($params['user_id']) && $params['user_id'] == $user_id) ) {
                auth()->guard('posuser')->logout();
                return response()->json([
                    'status' => true,
                    'route' => 'pos_login'
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'route' => 'pos_login'
            ]);
        }
    }

    public function setCurrency()
    {
        $params = request()->all();
        
        if (auth()->guard('posuser')->check()) {
            $user_id = auth()->guard('posuser')->user()->id;

            if (isset($params['user_id']) && $params['user_id'] == $user_id && isset($params['currency_code'])) {
                
                session()->put('currency', $params['currency_code']);
                
                return response()->json([
                    'status' => true,
                    'message' => 'Success: Currency changed successfully!',
                    'route' => 'pos_setting_basic',
                    'currency_code' => core()->getCurrentCurrencyCode(),
                    'currency_symbol' => core()->currencySymbol(core()->getCurrentCurrencyCode())
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Warning: Invalid User!'
                ]);
            }
        }
    }

    public function setLocale()
    {
        $params = request()->all();
        
        if (auth()->guard('posuser')->check()) {
            $user_id = auth()->guard('posuser')->user()->id;

            if (isset($params['user_id']) && $params['user_id'] == $user_id && isset($params['locale_code'])) {
                app()->setLocale($params['locale_code']);

                session()->put('locale', $params['locale_code']);
                
                return response()->json([
                    'status' => true,
                    'message' => 'Success: Locale changed successfully!',
                    'route' => 'pos_home'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Warning: Invalid User!'
                ]);
            }
        }
    }

    public function saveUser()
    {
        $params = request()->all();

        if (auth()->guard('posuser')->check()) {

            $posOutletUser = $this->posOutletUser->find($params['user_id']);
            
            if (isset($params['user_id']) && isset($posOutletUser->id) && ($params['user_id'] != $posOutletUser->id)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Warning: Invalid User!'
                ]);
            }

            if (!isset($params['low_stock'])) {
                if (! auth()->guard('posuser')->attempt(['username' => $posOutletUser->username, 'password' => $params['previous_password']])) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Warning: you have entered wrong previous password, try again!'
                    ]);
                }
    
                $params['password'] = bcrypt($params['password']);
            }

            $this->posOutletUser->update($params, $posOutletUser->id);
            
            return response()->json([
                'status' => true,
                'message' => 'Success: your record updated successfully!'
            ]);
        }
    }

    public function getDrawerDetails()
    {
        $params = request()->all();

        $posOutletUser = $this->posOutletUser->find($params['user_id']);
        
        if (isset($params['user_id']) && isset($posOutletUser->id) && ($params['user_id'] != $posOutletUser->id)) {
            return response()->json([
                'status' => false,
                'message' => 'Warning: Invalid User!'
            ]);
        }

        $posOutlet = $this->posOutlet->find($posOutletUser->outlet_id);

        if ($posOutlet->id) {
            return response()->json([
                'status' => true,
                'drawer_data' => $this->getDrawer(['id' => $posOutletUser->id, 'outlet_id' => $posOutletUser->outlet_id]),
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Warning: Invalid User\'s Outlet!'
            ]);
        }
    }

    public function getDrawer($data)
    {
        $drawer_data = [];

        $params = [
            'user_id' => $data['id'],
            'outlet_id' => $data['outlet_id'],
            'today_entry' => true,
            'limit' => 1,
        ];
        
        $posDrawer = $this->posDrawer->getDrawerRecords($params);

        if ( isset($posDrawer[0]->user_id) && ($posDrawer[0]->user_id == $data['id']) ) {
            $posDrawer = $posDrawer[0];
            $drawer_data = [
                'id' => $posDrawer->id,
                'modal_open' => 0,
                'user_id' => $posDrawer->user_id,
                'outlet_id' => $posDrawer->outlet_id,
                'base_currency' => $posDrawer->base_currency,
                'opening_amount' => $posDrawer->opening_amount,
                'converted_opening_amount' => core()->convertPrice($posDrawer->opening_amount, core()->getCurrentCurrencyCode()),
                'fc_opening_amount' => core()->currency($posDrawer->opening_amount),
                'base_symbol' => core()->currencySymbol($posDrawer->base_currency),
                'status' => $posDrawer->status,
                'created_at' => date_format($posDrawer->created_at, "Y/m/d"),
            ];
        } else {
            $params = [
                'user_id' => $data['id'],
                'outlet_id' => $data['outlet_id'],
                'duration_type' => 'monthly',
                'limit' => 1,
            ];
            $posDrawer = $this->posDrawer->getDrawerRecords($params);
            if ( isset($posDrawer[0]->user_id) && ($posDrawer[0]->user_id == $data['id']) ) {
                $posDrawer = $posDrawer[0];
                $drawer_data = [
                    'id' => $posDrawer->id,
                    'modal_open' => 1,
                    'user_id' => $posDrawer->user_id,
                    'outlet_id' => $posDrawer->outlet_id,
                    'base_currency' => $posDrawer->base_currency,
                    'opening_amount' => $posDrawer->opening_amount,
                    'converted_opening_amount' => core()->convertPrice($posDrawer->opening_amount, core()->getCurrentCurrencyCode()),
                    'fc_opening_amount' => core()->currency($posDrawer->opening_amount),
                    'base_symbol' => core()->currencySymbol($posDrawer->base_currency),
                    'status' => $posDrawer->status,
                    'created_at' => date_format($posDrawer->created_at, "Y/m/d"),
                ];
            }
        }

        return $drawer_data;
    }

    public function updateDrawer()
    {
        $params = request()->all();
        
        if (auth()->guard('posuser')->check()) {
            $posOutletUser = $this->posOutletUser->find($params['user_id']);

            if (isset($params['user_id']) && isset($posOutletUser->id) && ($params['user_id'] != $posOutletUser->id)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Warning: Invalid Drawer!'
                ]);
            }

            if ( !isset($params['update_status']) || (isset($params['update_status']) && $params['update_status']) ) {
                $data = [];
                $data['user_id'] = $posOutletUser->id;
                $data['outlet_id'] = $posOutletUser->outlet_id;
                $data['base_currency'] = core()->getBaseCurrencyCode();
                $front_currency_code = isset($params['currency_code']) ? $params['currency_code'] : core()->getChannelBaseCurrencyCode();
                $data['opening_currency'] = $front_currency_code;
                
                if ( core()->getBaseCurrencyCode() == $front_currency_code ) {
                    $amount = core()->convertToBasePrice($params['amount']);
                } else {
                    $amount = $params['amount'];
                }
                $data['opening_amount'] = $amount;
                $data['status'] = 1;
                $data['remark'] = $params['remark'];
                
                $posDrawer = $this->posDrawer->create($data);
                
                $posDrawer['converted_opening_amount'] = 0;
                if ($posDrawer->opening_amount) {
                    $posDrawer['converted_opening_amount'] = core()->convertPrice($posDrawer->opening_amount, isset($params['currency_code']) ?: core()->getChannelBaseCurrencyCode());
                    $posDrawer['fc_opening_amount'] = core()->currency($posDrawer->opening_amount);
                } else {
                    $posDrawer['fc_opening_amount'] = core()->currencySymbol(isset($params['currency_code']) ?: core()->getChannelBaseCurrencyCode()) . '0.00';
                }

                $posDrawer['base_symbol'] = core()->currencySymbol($posDrawer->base_currency);
                $posDrawer['modal_open'] = 0;
            
                $user_data = array();
                $user_data = $posOutletUser;
                $user_data['img_path'] = url('storage/' . $posOutletUser->image);

                $posOutlet = $this->posOutlet->find($user_data->outlet_id);
                $user_data['inventory_source_id'] = $posOutlet->inventory_source_id;

                $countryState = $this->countryState->findWhere(['country_code' => $posOutlet->country, 'code' => $posOutlet->state])->first();

                $user_data['outlet_address'] = [
                    'address1'  => $posOutlet->address,
                    'address2'  => '',
                    'country'   => $posOutlet->country,
                    'country_name' => core()->country_name($posOutlet->country),
                    'state'     => $posOutlet->state,
                    'state_name' => isset($countryState->default_name) ? $countryState->default_name : $posOutlet->state,
                    'postcode'  => $posOutlet->postcode,
                ];
                
                return response()->json([
                    'status' => true,
                    'message' => 'Success: Drawer\'s amount updated successfully!',
                    'route' => 'pos_home',
                    'user_data' => $user_data,
                    'drawer_data' => $posDrawer
                ]);
            } else {
                if (isset($params['update_status']) && !$params['update_status']) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Success: Drawer\'s amount updated successfully!',
                    ]);
                }
            }
        }
    }
}