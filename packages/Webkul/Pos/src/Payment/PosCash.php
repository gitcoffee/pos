<?php

namespace Webkul\Pos\Payment;

use Webkul\Payment\Payment\Payment;

class PosCash extends Payment
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $code  = 'pos_cash';

    public function getRedirectUrl()
    {
        
    }

    /**
     * Returns payment method additional information
     *
     * @return array
     */
    public function getAdditionalDetails()
    {
        return [
            'title' => 'Pos Payment',
            'value' => 'Cash Payment',
        ];
    }
}