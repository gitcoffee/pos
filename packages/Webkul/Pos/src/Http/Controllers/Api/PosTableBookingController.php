<?php

namespace Webkul\Pos\Http\Controllers\Api;

use DB;
use Illuminate\Support\Facades\Mail;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Pos\Repositories\PosTableRepository;
use Webkul\Pos\Repositories\PosTableBookingRepository;
use Webkul\Pos\Mail\NewBookingCustomerNotification;
use Webkul\Pos\Mail\NewBookingAgentNotification;
use Webkul\Pos\Http\Resources\RestaurantTableBooking as RestaurantTableBookingResource;
use Carbon\Carbon;

/**
 * PosTableBooking controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com>
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosTableBookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $_config;
    
    /**
     * CustomerRepository object
     *
     * @var array
     */
    protected $customerRepository;

    /**
     * PosTableRepository object
     *
     * @var array
     */
    protected $posTableRepository;

    /**
     * PosTableBookingRepository object
     *
     * @var array
     */
    protected $posTableBookingRepository;

    public function __construct(
        CustomerRepository $customerRepository,
        PosTableRepository $posTableRepository,
        PosTableBookingRepository $posTableBookingRepository
    )   {
        $this->middleware('posuser')->except(['getRestaurantTables']);
        
        $this->_config = request('_config');

        $this->customerRepository = $customerRepository;

        $this->posTableRepository = $posTableRepository;

        $this->posTableBookingRepository = $posTableBookingRepository;
    }

    public function getRestaurantBookedTables()
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

            return RestaurantTableBookingResource::collection($this->posTableBookingRepository->getAll());
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Warning: Invalid Pos agent.'
            ]);    
        }        
    }

    public function saveBooking()
    {
        $this->validate(request(), [
            'agent_id'          => 'required',
            'booking_id'        => 'required',
            'table_id'          => 'required',
            'customer_name'     => 'required|string|max:50',
            'customer_email'    => 'email',
            'customer_phone'    => 'numeric',
            'booked_seat'       => 'required|numeric',
            'booked_date'       => 'required',
            'booked_time_from'  => 'required',
            'booked_time_to'    => 'required',
        ]);

        $data = request()->all();

        if ( auth()->guard('posuser')->check() ) {

            $pos_user = auth()->guard('posuser')->user();

            if ( !isset($data['agent_id']) || (isset($data['agent_id']) && ($pos_user->id != $data['agent_id']))) {
                return response()->json([
                    'status'    => false,
                    'message'   => 'Warning: Invalid Pos agent.'
                ]);
            }

            if ( !$data['customer_email'] && !$data['customer_phone'] ) {
                return response()->json([
                    'status'    => false,
                    'message'   => 'Warning: Provide either customer email or mobile number.'
                ]);
            }

            $result = $this->validateTableSeat($data);

            if ( isset($result['status']) && $result['status'] ) {
                $data['status'] = 1;

                if ( !$data['customer_email'] && $data['customer_phone'] ) {
                    $data['customer_email'] = $data['customer_phone'] . core()->getConfigData('pos.restaurant.email.generate_email');
                }
                
                $customer = $this->customerRepository->findOneWhere(['email' =>  $data['customer_email']]);
                
                if ( $customer ) {
                    $data['customer_id'] = $customer->id;
                } else {
                    $name = explode(" ", $data['customer_name']);
                    $customer_data = [
                        'first_name'    => isset($name[0]) ? $name[0] : $data['customer_name'],
                        'last_name'     => isset($name[1]) ? $name[1] : ' ',
                        'email'         => $data['customer_email'],
                        'phone'         => $data['customer_phone'],
                        'gender'        => "Male",
                        'date_of_birth' => "",
                        'customer_group_id' => "2",
                        'password'      => bcrypt($data['customer_email']),
                        'is_verified'   => "1",
                    ];
                    
                    $customer = $this->customerRepository->create($customer_data);
                    $data['customer_id'] = $customer->id;
                }

                $pos_table_booked = $this->posTableBookingRepository->create($data);
    
                if ( isset($pos_table_booked->id) ) {
                    try {
                        
                        if ( core()->getConfigData('pos.restaurant.email.customer_booking_email') ) {
                            Mail::queue(new NewBookingCustomerNotification($pos_table_booked));
                        }

                        if ( core()->getConfigData('pos.restaurant.email.agent_booking_email') ) {
                            Mail::queue(new NewBookingAgentNotification($pos_table_booked));
                        }

                    } catch (\Exception $e) {
            
                    }
                    return response()->json([
                        'status'    => true,
                        'message'   => 'Success: Restaurant table booked successfully!',
                        'route'     => 'pos_restaurant_table_booked'
                    ]);
                }
            } else {
                return response()->json($result);
            }
        } else {
            return [
                'status'    => false,
                'message'   => 'Warning: Invaliid Pos user.'
            ];
        }
    }

    public function validateTableSeat($data)
    {
        $result = [
            'status' => true,
        ];

        $agent_table = $this->posTableRepository->findOneWhere([
            'agent_id'  => $data['agent_id'],
            'id'        => $data['table_id'],
        ]);

        if ( isset($agent_table->id) ) {
            if ( $agent_table->no_of_seat < $data['booked_seat'] ) {
                $result = [
                    'status'    => false,
                    'message'   => 'Warning: You can not book seats more than the total seats i.e. ' . $agent_table->no_of_seat . '.'
                ];
            }

            $current_date_time = Carbon::now()->format('Y-m-d') . ' ' . Carbon::now()->format('H:i:s');
            $current_date = Carbon::now()->format('Y-m-d');
            $current_time = Carbon::now()->format('H:i:s');

            $booked_seat = $this->posTableBookingRepository->scopeQuery(function ($query) use ($current_date_time, $data) {
                $query = $query->where('pos_restaurant_table_bookings.agent_id', '=', $data['agent_id'])->where('pos_restaurant_table_bookings.table_id', '=', $data['table_id'])
                ->where(DB::raw("CONCAT(pos_restaurant_table_bookings.booked_date, ' ', pos_restaurant_table_bookings.booked_time_from)"), '>=', $current_date_time)
                ->where('pos_restaurant_table_bookings.booked_date', '=', $data['booked_date'])
                ->orWhere(function($query_1) use($data) {
                    $query_1 = $query_1->where('pos_restaurant_table_bookings.booked_time_from', '>=', $data['booked_time_from'])
                    ->where('pos_restaurant_table_bookings.booked_time_to', '=<', $data['booked_time_from']);
                })
                ->orWhere(function($query_2) use($data) {
                    $query_2 = $query_2->where('pos_restaurant_table_bookings.booked_time_from', '>=', $data['booked_time_to'])
                    ->where('pos_restaurant_table_bookings.booked_time_to', '=<', $data['booked_time_to']);
                });

                return $query;
            })->sum('booked_seat');

            $remaining_seat = $agent_table->no_of_seat - $booked_seat;

            if ( $data['booked_seat'] > $remaining_seat ) {
                $booked_date = $data['booked_date'] . ', ' . $data['booked_time_from'] . ' - ' . $data['booked_time_to'];

                $result = [
                    'status'    => false,
                    'message'   => 'Warning: You can\'t book ' . $data['booked_seat'] . ' seats in this time slot ' . $booked_date . ', total available seats: ' . $remaining_seat  
                ];
            }
        }
        
        $current_date = Carbon::createFromTimeString(Carbon::now()->format('Y-m-d H:i:s'));
        
        $booked_date_time_from = Carbon::createFromTimeString($data['booked_date'] . ' ' . $data['booked_time_from']);
        
        $booked_date_time_to = Carbon::createFromTimeString($data['booked_date'] . ' ' . $data['booked_time_to']);

        if ( $booked_date_time_from < $current_date ) {
            $result = [
                'status'    => false,
                'message'   => 'Warning: Your Booking Date and Time must be grather than the Current Date Time.'
            ];
        }

        if ( $booked_date_time_from > $booked_date_time_to ) {
            $result = [
                'status'    => false,
                'message'   => 'Warning: Your Booking Time To must be grather than the Time From.'
            ];
        }
        
        return $result;
    }

    public function removeTableBooking()
    {
        $params = request()->all();

        if ( auth()->guard('posuser')->check() ) {

            $pos_user = auth()->guard('posuser')->user();

            if ( !isset($params['agent_id']) || (isset($params['agent_id']) && ($pos_user->id != $params['agent_id']))) {
                return $errors = [
                    'status' => false,
                    'message' => 'Warning: Invalid Pos User!'
                ];
            }

            $booked_seat = $this->posTableBookingRepository->findOneWhere([
                'agent_id' => $params['agent_id'],
                'booking_id' => $params['booking_id']
            ]);

            if (isset($booked_seat->booking_id) && $booked_seat->booking_id === $params['booking_id']) {
                
                $this->posTableBookingRepository->delete($booked_seat->id);

                return response()->json([
                    'status' => true,
                    'message' => 'Success: Pos restaurant table booking released successfully!',
                    'route'  => 'pos_restaurant_table_list'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Warning: Invalid table booking entry!'
                ]);
            }
        }
    }
}