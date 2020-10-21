<?php

namespace Webkul\Pos\Http\Controllers\Api;

use Webkul\Pos\Repositories\PosBankRepository;
use Webkul\Pos\Http\Resources\Bank as BankResource;

/**
 * PosBank controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com>
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosBankController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $_config;

    /**
     * PosBankRepository object
     *
     * @var array
     */
    protected $posBankRepository;

    public function __construct(PosBankRepository $posBankRepository)
    {
        $this->middleware('posuser');
        
        $this->_config = request('_config');

        $this->posBankRepository = $posBankRepository;
    }

    public function getBanks()
    {
        $params = request()->input();
        
        if ( auth()->guard('posuser')->check() ) {

            $pos_user = auth()->guard('posuser')->user();
            
            if ( !isset($params['user_id']) || (isset($params['user_id']) && ($pos_user->id != $params['user_id']))) {
                return response()->json([
                    'status'    => false,
                    'message'   => 'Warning: Invalid Pos Agent.'
                ]);
            }
            return response()->json([
                'status'    => true,
                'data'      => BankResource::collection($this->posBankRepository->findWhere([
                    'agent_id'  => $pos_user->id,
                    'status'    => true,
                ])),
            ]);
        } else {
            return response()->json([
                'status'    => false,
                'message'   => 'Warning: Invalid Pos Agent.'
            ]);    
        }        
    }
}