<?php

namespace Webkul\Pos\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * New BookingAgent Notification Mail class
 *
 * @author    Vivek Sharma <viveksh047@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class NewBookingAgentNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The Booking instance.
     *
     * @var Booking
     */
    public $booking;

    /**
     * Create a new message instance.
     *
     * @param mixed $booking
     * @return void
     */
    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $agent_details = $this->booking->agent;
        
        $query = $this->to($agent_details->email, ucfirst($agent_details->firstname) . ' ' . ucfirst($agent_details->lastname));
        
        if ( core()->getConfigData('pos.restaurant.email.custom_booking_email') ) {
            $query->cc(core()->getConfigData('pos.restaurant.email.custom_booking_email'));
        }

        $query->subject(trans('pos::app.admin.mail.booking.subject', ['booking_id' => $this->booking->booking_id]))
            ->view('pos::shop.emails.bookings.new-agent-booking')->with('booking', $this->booking);
        
            return $query; 
    }
}
