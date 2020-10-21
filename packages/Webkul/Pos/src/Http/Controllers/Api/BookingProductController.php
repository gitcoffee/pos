<?php

namespace Webkul\Pos\Http\Controllers\Api;

use Webkul\BookingProduct\Repositories\BookingProductRepository;
use Webkul\BookingProduct\Helpers\DefaultSlot as DefaultSlotHelper;
use Webkul\BookingProduct\Helpers\AppointmentSlot as AppointmentSlotHelper;
use Webkul\BookingProduct\Helpers\RentalSlot as RentalSlotHelper;
use Webkul\BookingProduct\Helpers\EventTicket as EventTicketHelper;
use Webkul\BookingProduct\Helpers\TableSlot as TableSlotHelper;

class BookingProductController extends Controller
{
    /**
     * @return array
     */
    protected $bookingHelpers = [];

    /**
     * Create a new helper instance.
     *
     * @param  \Webkul\BookingProduct\Repositories\BookingProductRepository  $bookingProductRepository
     * @param  \Webkul\BookingProduct\Helpers\DefaultSlot                    $defaultSlotHelper
     * @param  \Webkul\BookingProduct\Helpers\AppointmentSlot                $appointmentSlotHelper
     * @param  \Webkul\BookingProduct\Helpers\RentalSlot                     $rentalSlotHelper
     * @param  \Webkul\BookingProduct\Helpers\EventTicket                    $EventTicketHelper
     * @param  \Webkul\BookingProduct\Helpers\TableSlot                      $tableSlotHelper
     * @return void
     */
    public function __construct(
        BookingProductRepository $bookingProductRepository,
        DefaultSlotHelper $defaultSlotHelper,
        AppointmentSlotHelper $appointmentSlotHelper,
        RentalSlotHelper $rentalSlotHelper,
        EventTicketHelper $eventTicketHelper,
        TableSlotHelper $tableSlotHelper
    )
    {
        $this->bookingProductRepository = $bookingProductRepository;
        
        $this->bookingHelpers['default'] = $defaultSlotHelper;

        $this->bookingHelpers['appointment'] = $appointmentSlotHelper;

        $this->bookingHelpers['rental'] = $rentalSlotHelper;

        $this->bookingHelpers['event'] = $eventTicketHelper;

        $this->bookingHelpers['table'] = $tableSlotHelper;

        $this->middleware('posuser');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $params = request()->input();
        
        if ( auth()->guard('posuser')->check() ) {

            $pos_user = auth()->guard('posuser')->user();
            
            if ( !isset($params['user_id']) || (isset($params['user_id']) && ($pos_user->id != $params['user_id']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Warning: Invalid Pos Agent.'
                ]);
            }
            $bookingProduct = $this->bookingProductRepository->find($params['booking_id']);

            return response()->json([
                'data' => $this->bookingHelpers[$bookingProduct->type]->getSlotsByDate($bookingProduct, $params['date']),
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Warning: Invalid Pos Agent.'
            ]);    
        }    

        
    }
}