<?php

Route::group(['middleware' => ['web', 'locale', 'currency']], function () {
    Route::prefix('pos')->group(function () {

        Route::get('/', 'Webkul\Pos\Http\Controllers\Api\AuthController@show')->defaults('_config', [
            'view' => 'pos::shop.index'
        ])->name('pos.api.auth.show');

        Route::get('/{any}', 'Webkul\Pos\Http\Controllers\Api\AuthController@show')->defaults('_config', [
            'view' => 'pos::shop.index',
        ])->where('any', '.*');

    });
    
    Route::prefix('api/pos')->group(function () {

        Route::get('/userlogin', 'Webkul\Pos\Http\Controllers\Api\AuthController@checkUserLogin');

        Route::get('/config', 'Webkul\Pos\Http\Controllers\Api\PosConfigController@getPosConfig');

        Route::get('/navmenus', 'Webkul\Pos\Http\Controllers\Api\PosConfigController@getLeftNavMenus');

        Route::get('/categories','Webkul\Pos\Http\Controllers\Api\PosProductController@getPosCategories');

        //Get Taxes
        Route::get('/getTaxes', 'Webkul\Pos\Http\Controllers\Api\ResourceController@index')->defaults('_config', [
            'repository' => 'Webkul\Tax\Repositories\TaxCategoryRepository',
            'resource' => 'Webkul\Pos\Http\Resources\Taxes'
        ]);

        //Get Customer Lists
        Route::get('/getCustomers', 'Webkul\Pos\Http\Controllers\Api\PosCustomerController@getCustomers')->defaults('_config', [
            'repository' => 'Webkul\Pos\Repositories\PosCustomerRepository',
            'resource' => 'Webkul\Pos\Http\Resources\Customer'
        ]);

        //Get Customer Group Lists
        Route::get('/getGroups', 'Webkul\Pos\Http\Controllers\Api\PosCustomerController@getCustomerGroups')->defaults('_config', [
            'repository' => 'Webkul\Customer\Repositories\CustomerGroupRepository',
            'resource' => 'Webkul\Pos\Http\Resources\CustomerGroup'
        ]);

        Route::prefix('auth')->group(function () {
            Route::post('/login', 'Webkul\Pos\Http\Controllers\Api\AuthController@login');
            
            Route::group(['middleware' => 'auth:posuser'], function() {

                Route::get('/user', 'Webkul\Pos\Http\Controllers\Api\AuthController@user');

                Route::post('/changeCurrency', 'Webkul\Pos\Http\Controllers\Api\AuthController@setCurrency');

                Route::post('/changeLocale', 'Webkul\Pos\Http\Controllers\Api\AuthController@setLocale');

                Route::post('/updateUser', 'Webkul\Pos\Http\Controllers\Api\AuthController@saveUser');
                
                Route::post('/getDrawerDetails', 'Webkul\Pos\Http\Controllers\Api\AuthController@getDrawerDetails');

                Route::post('/updateDrawer', 'Webkul\Pos\Http\Controllers\Api\AuthController@updateDrawer');

                //Get Bank Links
                Route::get('/getBanks', 'Webkul\Pos\Http\Controllers\Api\PosBankController@getBanks')->defaults('_config', [
                    'repository' => 'Webkul\Pos\Repositories\PosBankRepository',
                    'resource' => 'Webkul\Pos\Http\Resources\Bank'
                ]);

                //Outlet Product's Categories routes
                Route::post('/productCategories', 'Webkul\Pos\Http\Controllers\Api\PosProductCategoriesController@getPosProductCategories')->defaults('_config', [
                    'repository' => 'Webkul\Pos\Repositories\PosProductCategoriesRepository',
                    'resource' => 'Webkul\Pos\Http\Resources\ProductCategories'
                ]);

                //validate the custom product's sku
                Route::get('/validateSku', 'Webkul\Pos\Http\Controllers\Api\PosProductController@validateSku')->defaults('_config', [
                    'Webkul\Pos\Repositories\PosOutletProductRepository'
                ]);

                //Outlet Products routes
                Route::get('/outletProducts', 'Webkul\Pos\Http\Controllers\Api\PosProductController@getOutletProducts')->defaults('_config', [
                    'repository' => 'Webkul\Pos\Repositories\PosOutletProductRepository',
                    'resource' => 'Webkul\Pos\Http\Resources\OutletProduct'
                ]);

                //Get Discounts
                Route::get('/getDiscounts', 'Webkul\Pos\Http\Controllers\Api\PosDiscountController@getUserDiscounts')->defaults('_config', [
                    'repository' => 'Webkul\Pos\Repositories\PosDiscountRepository',
                    'resource' => 'Webkul\Pos\Http\Resources\Discount'
                ]);

                //Save Discounts
                Route::post('/addDiscount', 'Webkul\Pos\Http\Controllers\Api\PosDiscountController@saveDiscount')->defaults('_config', [
                    'repository' => 'Webkul\Pos\Repositories\PosDiscountRepository',
                    'resource' => 'Webkul\Pos\Http\Resources\Discount'
                ]);
                
                //Edit Discounts
                Route::post('/editDiscount', 'Webkul\Pos\Http\Controllers\Api\PosDiscountController@updateDiscount')->defaults('_config', [
                    'repository' => 'Webkul\Pos\Repositories\PosDiscountRepository',
                    'resource' => 'Webkul\Pos\Http\Resources\Discount'
                ]);
                
                // Remove Discount
                Route::post('/deleteDiscount', 'Webkul\Pos\Http\Controllers\Api\PosDiscountController@removeDiscount')->defaults('_config', [
                    'repository' => 'Webkul\Pos\Repositories\PosDiscountRepository',
                    'resource' => 'Webkul\Pos\Http\Resources\Discount'
                ]);

                //Get LowStock Requested Products
                Route::get('/getLowRequestedProducts', 'Webkul\Pos\Http\Controllers\Api\PosProductRequestController@getLowStockRequestedProducts')->defaults('_config', [
                    'repository' => 'Webkul\Pos\Repositories\PosProductRequestRepository',
                    'resource' => 'Webkul\Pos\Http\Resources\ProductRequest'
                ]);

                //Save LowStock Product Request
                Route::post('/addStockRequest', 'Webkul\Pos\Http\Controllers\Api\PosProductRequestController@saveRequest')->defaults('_config', [
                    'repository' => 'Webkul\Pos\Repositories\PosProductRequestRepository',
                    'resource' => 'Webkul\Pos\Http\Resources\ProductRequest'
                ]);
                
                //Update LowStock Product Request
                Route::post('/editStockRequest', 'Webkul\Pos\Http\Controllers\Api\PosProductRequestController@updateRequest')->defaults('_config', [
                    'repository' => 'Webkul\Pos\Repositories\PosProductRequestRepository',
                    'resource' => 'Webkul\Pos\Http\Resources\ProductRequest'
                ]);
                
                //Remove LowStock Product Request
                Route::post('/deleteRequest', 'Webkul\Pos\Http\Controllers\Api\PosProductRequestController@removeRequest')->defaults('_config', [
                    'repository' => 'Webkul\Pos\Repositories\PosProductRequestRepository',
                    'resource' => 'Webkul\Pos\Http\Resources\ProductRequest'
                ]);

                //Send LowStock Product Request
                Route::post('/sentRequest', 'Webkul\Pos\Http\Controllers\Api\PosProductRequestController@sendRequest')->defaults('_config', [
                    'repository' => 'Webkul\Pos\Repositories\PosProductRequestRepository',
                    'resource' => 'Webkul\Pos\Http\Resources\ProductRequest'
                ]);

                //Save Customer Lists
                Route::post('/addCustomer', 'Webkul\Pos\Http\Controllers\Api\PosCustomerController@saveCustomer')->defaults('_config', [
                    'repository' => 'Webkul\Pos\Repositories\PosCustomerRepository',
                    'resource' => 'Webkul\Pos\Http\Resources\Customer'
                ]);

                //Update Customer Lists
                Route::post('/editCustomer', 'Webkul\Pos\Http\Controllers\Api\PosCustomerController@updateCustomer')->defaults('_config', [
                    'repository' => 'Webkul\Pos\Repositories\PosCustomerRepository',
                    'resource' => 'Webkul\Pos\Http\Resources\Customer'
                ]);

                //Save Orders
                Route::post('/addOrder', 'Webkul\Pos\Http\Controllers\Api\PosOrderController@saveOrder')->defaults('_config', [
                    'repository' => 'Webkul\Pos\Repositories\PosOrderRepository',
                    'resource' => 'Webkul\Pos\Http\Resources\Order'
                ]);

                //get Orders
                Route::get('/getOrders', 'Webkul\Pos\Http\Controllers\Api\PosOrderController@getOrders')->defaults('_config', [
                    'repository' => 'Webkul\Pos\Repositories\PosOrderRepository',
                    'resource' => 'Webkul\Pos\Http\Resources\Order'
                ]);

                //get Orders
                Route::get('/slots', 'Webkul\Pos\Http\Controllers\Api\BookingProductController@index')->defaults('_config', [
                    'repository' => 'Webkul\BookingProduct\Repositories\BookingProductRepository'
                ]);

                //Pos Restaurant tables routes
                Route::get('/getTables', 'Webkul\Pos\Http\Controllers\Api\PosTableController@getRestaurantTables')->defaults('_config', [
                    'repository' => 'Webkul\Pos\Repositories\PosTableRepository',
                    'resource' => 'Webkul\Pos\Http\Resources\RestaurantTable'
                ]);

                Route::post('/addTable', 'Webkul\Pos\Http\Controllers\Api\PosTableController@saveTable')->defaults('_config', [
                    'repository' => 'Webkul\Pos\Repositories\PosTableRepository',
                    'resource' => 'Webkul\Pos\Http\Resources\RestaurantTable'
                ]);

                Route::get('/getBookedTables', 'Webkul\Pos\Http\Controllers\Api\PosTableBookingController@getRestaurantBookedTables')->defaults('_config', [
                    'repository' => 'Webkul\Pos\Repositories\PosTableBookingRepository',
                    'resource' => 'Webkul\Pos\Http\Resources\RestaurantTableBooking'
                ]);

                Route::post('/addBooking', 'Webkul\Pos\Http\Controllers\Api\PosTableBookingController@saveBooking')->defaults('_config', [
                    'repository' => 'Webkul\Pos\Repositories\PosTableBookingRepository',
                    'resource' => 'Webkul\Pos\Http\Resources\RestaurantTableBooking'
                ]);
                
                Route::post('/removeBooking', 'Webkul\Pos\Http\Controllers\Api\PosTableBookingController@removeTableBooking')->defaults('_config', [
                    'repository' => 'Webkul\Pos\Repositories\PosTableBookingRepository',
                    'resource' => 'Webkul\Pos\Http\Resources\RestaurantTableBooking'
                ]);

                Route::post('/destroy', 'Webkul\Pos\Http\Controllers\Api\AuthController@destroy');
            });
        });
    });
});
