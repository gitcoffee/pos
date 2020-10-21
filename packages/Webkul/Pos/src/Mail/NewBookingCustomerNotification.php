<?php

namespace Webkul\Pos\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * New BookingCustomer Notification Mail class
 *
 * @author    Vivek Sharma <viveksh047@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class NewBookingCustomerNotification extends Mailable
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
        return $this->to($this->booking->customer_email, $this->booking->customer_name)
                ->subject(trans('pos::app.admin.mail.booking.subject', ['booking_id' => $this->booking->booking_id]))
                ->view('pos::shop.emails.bookings.new-customer-booking')->with('booking', $this->booking);
    }
}
