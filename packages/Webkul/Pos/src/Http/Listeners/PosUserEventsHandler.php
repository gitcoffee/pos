<?php

namespace Webkul\Pos\Http\Listeners;
use Cookie;
use Cart;

/**
 * PosUser event handler
 *
 * @author    Vivek Sharma <viveksh047@webkul.com>
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosUserEventsHandler {

    /**
     * Handle User login events.
     */
    public function onPosUserLogin($event)
    {
        /**
         * handle the user login event to manage the after login, if the user has added any products as guest then
         * the cart items from session will be transferred from cookie to the cart table in the database.
         *
         * Check whether cookie is present or not and then check emptiness and then do the appropriate actions.
         */
        // Cart::mergeCart();
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     * @return void
     */
    public function subscribe($events)
    {
        $events->listen('user.after.login', 'Webkul\Pos\Http\Listeners\PosUserEventsHandler@onPosUserLogin');
    }
}