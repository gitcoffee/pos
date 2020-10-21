<?php

return [
    [
        'key' => 'pos',
        'name' => 'pos::shop.home.nav-left.pos',
        'id' => 'menu-pos',
        'sort' => 1,
        'iconClass' => 'nav-pos-icon',
        'route' => '/pos/home'
    ], [
        'key' => 'sales',
        'name' => 'pos::shop.home.nav-left.sale',
        'id' => 'menu-sale',
        'sort' => 2,
        'iconClass' => 'fa fa-file',
        'route' => '/pos/sales/history'
    ], [
        'key' => 'customer',
        'name' => 'pos::shop.home.nav-left.customer',
        'id' => 'menu-customer',
        'sort' => 3,
        'iconClass' => 'fa fa-user-circle',
        'route' => '/pos/customer'
    ], [
        'key' => 'cashier',
        'name' => 'pos::shop.home.nav-left.cashier',
        'id' => 'menu-cashier',
        'sort' => 4,
        'iconClass' => 'fa fa-user',
        'route' => '/pos/cashier/closecounter'
    ],  [
        'key' => 'product',
        'name' => 'pos::shop.home.nav-left.product',
        'id' => 'menu-product',
        'sort' => 6,
        'iconClass' => 'fa fa-cubes',
        'route' => '/pos/product/lowstock'
    ],  [
        'key' => 'restaurant',
        'name' => 'pos::shop.home.nav-left.restaurant',
        'id' => 'menu-restarant',
        'sort' => 7,
        'iconClass' => 'fa fa-home',
        'route' => '/pos/restaurant/table-list'
    ], [
        'key' => 'setting',
        'name' => 'pos::shop.home.nav-left.setting',
        'id' => 'menu-setting',
        'sort' => 7,
        'iconClass' => 'fa fa-cog',
        'route' => '/pos/setting/discount'
    ]
];