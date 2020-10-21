<?php

namespace Webkul\Pos\Http\Controllers\Api;

use Illuminate\Validation\Rule;
use Webkul\Pos\Repositories\PosTableRepository;
use Webkul\Pos\Http\Resources\RestaurantTable as RestaurantTableResource;

/**
 * PosTable controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com>
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosTableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $_config;

    /**
     * PosTableRepository object
     *
     * @var array
     */
    protected $posTableRepository;

    public function __construct(PosTableRepository $posTableRepository)
    {
        $this->middleware('posuser')->except(['getRestaurantTables']);
        
        $this->_config = request('_config');

        $this->posTableRepository = $posTableRepository;
    }

    public function getRestaurantTables()
    {
        $params = request()->input();
     
        if ( auth()->guard('posuser')->check() ) {

            $pos_user = auth()->guard('posuser')->user();
            
            if ( !isset($params['user_id']) || (isset($params['user_id']) && ($pos_user->id != $params['user_id']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Warning: Invalid Pos agent.'
                ]);
            }

            return RestaurantTableResource::collection($this->posTableRepository->getAll());
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Warning: Invalid Pos agent.'
            ]);    
        }        
    }

    public function saveTable()
    {
        $data = request()->all();

        $this->validate(request(), [
            'name'          => 'required|max:50',
            'type'          => 'string|required',
            'status'        => 'required',
            'no_of_seat'    => 'required|numeric',
            'agent_id'      => 'required|numeric',
            'position'  => [
                'required',
                Rule::unique('pos_restaurant_tables')->where(function ($query) use($data) {
                    return $query->where('position', $data['position'])
                    ->where('agent_id', $data['agent_id']);
                })
            ]
        ]);

        if ( auth()->guard('posuser')->check() ) {

            $pos_user = auth()->guard('posuser')->user();

            if ( !isset($data['agent_id']) || (isset($data['agent_id']) && ($pos_user->id != $data['agent_id']))) {
                return response()->json([
                    'status'    => false,
                    'message'   => 'Warning: Invalid Pos agent.'
                ]);
            }

            $agentTables = $this->posTableRepository->findByField(['agent_id' => $pos_user->id]);

            if ( count($agentTables) >= core()->getConfigData('pos.restaurant.general.agent_table_max')) {
                return response()->json([
                    'status'    => false,
                    'message'   => 'Warning: you can not create table more than ' . core()->getConfigData('pos.restaurant.general.agent_table_max') . ' tables.'
                ]);
            }

            $pos_table = $this->posTableRepository->create($data);

            if ( isset($pos_table->id) ) {
                return response()->json([
                    'status'    => true,
                    'message'   => 'Success: Restaurant table created successfully!',
                    'route'     => 'pos_restaurant'
                ]);
            }
        }
    }
}