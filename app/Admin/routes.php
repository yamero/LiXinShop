<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');

    $router->resource('users', UsersController::class);

    $router->resource('products', ProductsController::class);

    $router->resource('orders', OrdersController::class);

    $router->resource('coupon_codes', CouponCodesController::class);

    $router->post('orders/{order}/ship', 'OrdersController@ship')->name('admin.orders.ship');

    $router->post('orders/{order}/refund', 'OrdersController@handleRefund')->name('admin.orders.handle_refund');

});
