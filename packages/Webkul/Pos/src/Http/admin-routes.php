<?php

Route::group(['middleware' => ['web']], function () {

    Route::prefix('admin/pos')->group(function () {

        Route::group(['middleware' => ['admin']], function () {

            //DataGrid Export
            Route::post('/export', 'Webkul\Pos\Http\Controllers\Admin\ExportController@export')->name('pos.datagrid.export');

            // POS Product Routes
            Route::get('/products', 'Webkul\Pos\Http\Controllers\Admin\ProductsController@index')->defaults('_config', [
                'view' => 'pos::admin.products.index'
            ])->name('admin.pos.products.index');
            
            Route::get('/products/generateBarcode/{id}', 'Webkul\Pos\Http\Controllers\Admin\ProductsController@generateBarcode')->name('admin.pos.products.generateBarcode');

            Route::get('/products/printBarcode/{id}', 'Webkul\Pos\Http\Controllers\Admin\ProductsController@printBarcode')->defaults('_config', [
                'view' => 'pos::admin.products.print'
            ])->name('admin.pos.products.printBarcode');

            Route::post('/products/massupdate','Webkul\Pos\Http\Controllers\Admin\ProductsController@massupdate')->defaults('_config', [
                'barcode_view' => 'pos::admin.products.index',
                'print_view' => 'pos::admin.products.print',
                'redirect' => 'admin.pos.products.index'
            ])->name('admin.pos.products.massupdate');

            // POS Product Request Routes
            Route::get('/requests', 'Webkul\Pos\Http\Controllers\Admin\ProductRequestController@index')->defaults('_config', [
                'view' => 'pos::admin.requests.index'
            ])->name('admin.pos.requests.index');
            
            Route::get('/requests/view/{id}', 'Webkul\Pos\Http\Controllers\Admin\ProductRequestController@view')->defaults('_config', [
                'view' => 'pos::admin.requests.view'
            ])->name('admin.pos.requests.view');
            
            Route::put('/requests/view/{id}', 'Webkul\Pos\Http\Controllers\Admin\ProductRequestController@update')->defaults('_config', [
                'redirect' => 'admin.pos.requests.index'
            ])->name('admin.pos.requests.update');

            Route::post('/requests/massupdate','Webkul\Pos\Http\Controllers\Admin\ProductRequestController@massupdate')->defaults('_config', [
                'redirect' => 'admin.pos.requests.index'
            ])->name('admin.pos.requests.massupdate');

            // POS User Routes
            Route::get('/users', 'Webkul\Pos\Http\Controllers\Admin\UsersController@index')->defaults('_config', [
                'view' => 'pos::admin.users.users.index'
            ])->name('admin.pos.users.index');

            Route::get('/users/create', 'Webkul\Pos\Http\Controllers\Admin\UsersController@create')->defaults('_config', [
                'view' => 'pos::admin.users.users.create'
            ])->name('admin.pos.users.create');

            Route::post('/users/create', 'Webkul\Pos\Http\Controllers\Admin\UsersController@store')->defaults('_config', [
                'redirect' => 'admin.pos.users.index'
            ])->name('admin.pos.users.store');

            Route::get('/users/edit/{id}', 'Webkul\Pos\Http\Controllers\Admin\UsersController@edit')->defaults('_config', [
                'view' => 'pos::admin.users.users.edit'
            ])->name('admin.pos.users.edit');
            
            Route::put('/users/edit/{id}', 'Webkul\Pos\Http\Controllers\Admin\UsersController@update')->defaults('_config', [
                'redirect' => 'admin.pos.users.index'
            ])->name('admin.pos.users.update');

            Route::post('/users/delete/{id}', 'Webkul\Pos\Http\Controllers\Admin\UsersController@destroy')->name('admin.pos.users.delete');

            Route::post('/users/massdelete', 'Webkul\Pos\Http\Controllers\Admin\UsersController@massDestroy')->defaults('_config', [
                'redirect' => 'admin.pos.users.index'
            ])->name('admin.pos.users.massdelete');

            // Route::get('/outletproducts/{id}', 'Webkul\Pos\Http\Controllers\Admin\OutletsController@outletProducts')->defaults('_config', [
            //     'view' => 'pos::admin.users.outlets.assign'
            // ])->name('admin.pos.outlets.assign');

            // POS Outlet Routes
            Route::get('/outlets', 'Webkul\Pos\Http\Controllers\Admin\OutletsController@index')->defaults('_config', [
                'view' => 'pos::admin.users.outlets.index'
            ])->name('admin.pos.outlets.index');

            Route::get('/outlets/create', 'Webkul\Pos\Http\Controllers\Admin\OutletsController@create')->defaults('_config', [
                'view' => 'pos::admin.users.outlets.create'
            ])->name('admin.pos.outlets.create');

            Route::post('/outlets/create', 'Webkul\Pos\Http\Controllers\Admin\OutletsController@store')->defaults('_config', [
                'redirect' => 'admin.pos.outlets.index'
            ])->name('admin.pos.outlets.store');

            Route::get('/outlets/edit/{id}', 'Webkul\Pos\Http\Controllers\Admin\OutletsController@edit')->defaults('_config', [
                'view' => 'pos::admin.users.outlets.edit'
            ])->name('admin.pos.outlets.edit');

            Route::put('/outlets/edit/{id}', 'Webkul\Pos\Http\Controllers\Admin\OutletsController@update')->defaults('_config', [
                'redirect' => 'admin.pos.outlets.index'
            ])->name('admin.pos.outlets.update');

            Route::get('/outlets/assign/{id}','Webkul\Pos\Http\Controllers\Admin\OutletsController@assignProduct')->defaults('_config', [
                'view' => 'pos::admin.users.outlets.assign',
                'redirect' => 'admin.pos.outlets.index'
            ])->name('admin.pos.outlets.assign');

            Route::post('/outlets/assignupdate', 'Webkul\Pos\Http\Controllers\Admin\OutletsController@assignSave')->defaults('_config', [
                'redirect' => 'admin.pos.outlets.index'
            ])->name('admin.pos.outlets.assignupdate');

            Route::post('/outlets/massassign', 'Webkul\Pos\Http\Controllers\Admin\OutletsController@massAssign')->defaults('_config', [
                'redirect' => 'admin.pos.outlets.index'
            ])->name('admin.pos.outlets.massassign');

            Route::post('/outlets/massunassign', 'Webkul\Pos\Http\Controllers\Admin\OutletsController@massUnassign')->defaults('_config', [
                'redirect' => 'admin.pos.outlets.index'
            ])->name('admin.pos.outlets.massunassign');

            Route::post('/outlets/delete/{id}', 'Webkul\Pos\Http\Controllers\Admin\OutletsController@destroy')->name('admin.pos.outlets.delete');

            Route::post('/outlets/massdelete', 'Webkul\Pos\Http\Controllers\Admin\OutletsController@massDestroy')->defaults('_config', [
                'redirect' => 'admin.pos.outlets.index'
            ])->name('admin.pos.outlets.massdelete');

            // POS Report Routes
            Route::get('/reports', 'Webkul\Pos\Http\Controllers\Admin\ReportsController@index')->defaults('_config', [
                'view' => 'pos::admin.reports.index'
            ])->name('admin.pos.reports.index');

            //product search for linked products
            Route::get('/reports/search', 'Webkul\Pos\Http\Controllers\Admin\ReportsController@orderedProductSearch')->defaults('_config', [
                'view' => 'pos::admin.reports.index'
            ])->name('admin.pos.reports.orderedproductsearch');

            // POS Order Routes
            Route::get('/orders', 'Webkul\Pos\Http\Controllers\Admin\OrdersController@index')->defaults('_config', [
                'view' => 'pos::admin.orders.index'
            ])->name('admin.pos.orders.index');

            // POS Credit/Debit Banks Routes
            Route::get('/banks', 'Webkul\Pos\Http\Controllers\Admin\BanksController@index')->defaults('_config', [
                'view' => 'pos::admin.banks.index'
            ])->name('admin.pos.banks.index');

            Route::get('/banks/create', 'Webkul\Pos\Http\Controllers\Admin\BanksController@create')->defaults('_config', [
                'view' => 'pos::admin.banks.create'
            ])->name('admin.pos.banks.create');

            Route::post('/banks/create', 'Webkul\Pos\Http\Controllers\Admin\BanksController@store')->defaults('_config', [
                'redirect' => 'admin.pos.banks.index'
            ])->name('admin.pos.banks.store');

            Route::get('/banks/edit/{id}', 'Webkul\Pos\Http\Controllers\Admin\BanksController@edit')->defaults('_config', [
                'view' => 'pos::admin.banks.edit'
            ])->name('admin.pos.banks.edit');

            Route::put('/banks/edit/{id}', 'Webkul\Pos\Http\Controllers\Admin\BanksController@update')->defaults('_config', [
                'redirect' => 'admin.pos.banks.index'
            ])->name('admin.pos.banks.update');

            Route::post('/banks/delete/{id}', 'Webkul\Pos\Http\Controllers\Admin\BanksController@destroy')->name('admin.pos.banks.delete');

            Route::post('/banks/massdelete', 'Webkul\Pos\Http\Controllers\Admin\BanksController@massDestroy')->defaults('_config', [
                'redirect' => 'admin.pos.banks.index'
            ])->name('admin.pos.banks.massdelete');

            Route::prefix('restaurant')->group(function () {
                // POS Restaurants Routes
                Route::get('/tables', 'Webkul\Pos\Http\Controllers\Admin\TablesController@index')->defaults('_config', [
                    'view' => 'pos::admin.restaurants.tables.index'
                ])->name('admin.pos.restaurants.tables.index');

                Route::get('/tables/create', 'Webkul\Pos\Http\Controllers\Admin\TablesController@create')->defaults('_config', [
                    'view' => 'pos::admin.restaurants.tables.create'
                ])->name('admin.pos.restaurants.tables.create');

                Route::post('/tables/create', 'Webkul\Pos\Http\Controllers\Admin\TablesController@store')->defaults('_config', [
                    'redirect' => 'admin.pos.restaurants.tables.index'
                ])->name('admin.pos.restaurants.tables.store');

                Route::get('/tables/edit/{id}', 'Webkul\Pos\Http\Controllers\Admin\TablesController@edit')->defaults('_config', [
                    'view' => 'pos::admin.restaurants.tables.edit'
                ])->name('admin.pos.restaurants.tables.edit');
                
                Route::put('/tables/edit/{id}', 'Webkul\Pos\Http\Controllers\Admin\TablesController@update')->defaults('_config', [
                    'redirect' => 'admin.pos.restaurants.tables.index'
                ])->name('admin.pos.restaurants.tables.update');

                Route::post('/tables/delete/{id}', 'Webkul\Pos\Http\Controllers\Admin\TablesController@destroy')->name('admin.pos.restaurants.tables.delete');

                Route::post('/tables/massdelete', 'Webkul\Pos\Http\Controllers\Admin\TablesController@massDestroy')->defaults('_config', [
                    'redirect' => 'admin.pos.restaurants.tables.index'
                ])->name('admin.pos.restaurants.tables.massdelete');

                Route::get('/booked-history', 'Webkul\Pos\Http\Controllers\Admin\BookedHistoryController@index')->defaults('_config', [
                    'view' => 'pos::admin.restaurants.booked-history.index'
                ])->name('admin.pos.restaurants.booked-history.index');

                Route::post('/booking/delete/{id}', 'Webkul\Pos\Http\Controllers\Admin\BookingHistoryController@destroy')->name('admin.pos.restaurants.bookings.delete');
            });

        });
    });
});
