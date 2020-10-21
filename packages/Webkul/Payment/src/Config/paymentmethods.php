<?php
return [
    'cashondelivery'  => [
        'code'        => 'cashondelivery',
        'title'       => 'Cash On Delivery',
        'description' => 'Cash On Delivery',
        'class'       => 'Webkul\Payment\Payment\CashOnDelivery',
        'active'      => true,
        'sort'        => 1,
    ],

    'moneytransfer'   => [
        'code'        => 'moneytransfer',
        'title'       => 'Money Transfer',
        'description' => 'Money Transfer',
        'class'       => 'Webkul\Payment\Payment\MoneyTransfer',
        'active'      => true,
        'sort'        => 2,
    ],

    'paypal_standard' => [
        'code'             => 'paypal_standard',
        'title'            => 'Paypal Standard',
        'description'      => 'Paypal Standard',
        'class'            => 'Webkul\Paypal\Payment\Standard',
        'sandbox'          => true,
        'active'           => true,
        'business_account' => 'test@webkul.com',
        'sort'             => 3,
    ],
    
    'pos_cash' => [
        'code'             => 'pos_cash',
        'title'            => 'Cash Payment',
        'description'      => 'Cash Payment',
        'class'            => 'Webkul\Pos\Payment\PosCash',
        'active'           => true,
        'sort'             => 1,
    ],
    
    'pos_card' => [
        'code'             => 'pos_card',
        'title'            => 'Card Payment',
        'description'      => 'Card Payment',
        'class'            => 'Webkul\Pos\Payment\PosCard',
        'active'           => true,
        'sort'             => 1,
    ]
];