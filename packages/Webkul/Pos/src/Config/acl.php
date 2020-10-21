<?php

return [
    [
        'key' => 'pos.users',
        'name' => 'pos::app.admin.acl.users',
        'route' => 'admin.pos.users.index',
        'sort' => 1,
    ], [
        'key' => 'pos.users.users',
        'name' => 'pos::app.admin.acl.users',
        'route' => 'admin.pos.users.index',
        'sort' => 1,
    ], [
        'key' => 'pos.users.outlets',
        'name' => 'pos::app.admin.acl.outlets',
        'route' => 'admin.pos.outlets.index',
        'sort' => 2,
    ],  [
        'key' => 'pos',
        'name' => 'pos::app.admin.acl.pos',
        'route' => 'admin.pos.products.index',
        'sort' => 2,
    ], [
        'key' => 'pos.products',
        'name' => 'pos::app.admin.acl.products',
        'route' => 'admin.pos.products.index',
        'sort' => 1,
    ], [
        'key' => 'pos.orders',
        'name' => 'pos::app.admin.acl.orders',
        'route' => 'admin.pos.orders.index',
        'sort' => 3,
    ], [
        'key' => 'pos.requests',
        'name' => 'pos::app.admin.acl.requests',
        'route' => 'admin.pos.requests.index',
        'sort' => 4,
    ],  [
        'key' => 'pos.banks',
        'name' => 'pos::app.admin.acl.banks',
        'route' => 'admin.pos.banks.index',
        'sort' => 5,
    ],  [
        'key' => 'pos.restaurant.tables',
        'name' => 'pos::app.admin.layouts.restaurants',
        'route' => 'admin.pos.restaurants.tables.index',
        'sort' => 6,
    ],  [
        'key' => 'pos.restaurant.booking',
        'name' => 'pos::app.admin.layouts.table-booking-history',
        'route' => 'admin.pos.restaurants.booked-history.index',
        'sort' => 7,
    ]
];
