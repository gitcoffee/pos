<?php

return [
    'login' => [
        'page-title'    => 'Pos Login',
        'user'          => [
            'login-form'            => [
                'username'          => 'User Name',
                'password'          => 'Password',
                'remember-password' => 'Remember Password',
                'button_title'      => 'Log In',
                'forgot_passward'   => 'Forgot Password?',
                'footer-note'       => 'A Product Of <a href=":url" target="_blank">WEBKUL</a>',
                'invalid-creds'     => 'Warning: Please check your credentials and try again!',
                'unauth-user'       => 'Warning: You are not pos authorize user, please contact to admin!',
            ]
        ]
    ],
    'home'  => [
        'page-title'        => 'Pos System',
        'enable-pos-status' => 'Warning: Please enable pos extension status from the configuation.',
        'nav-top'           => [
            'heading'               => 'Point Of Sale',
            'search-placeholder'    => 'Search Product By Name, SKU',
            'cashier'               => 'Cashier',
        ],

        'nav-left'  => [
            'pos'           => 'POS',
            'sale'          => 'Sales',
            'customer'      => 'Customer',
            'cashier'       => 'Cashier',
            'product'       => 'Products',
            'restaurant'    => 'Restaurant',
            'setting'       => 'Setting',
        ],
    ],
];