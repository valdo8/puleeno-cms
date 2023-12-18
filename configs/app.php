<?php

return [

    /*
     * Mô tả ứng dụng
     */

    'name' => 'My Application',
    'version' => '1.0.0',
    'author' => 'John Doe',

    /*
     * Cổng
     */

    'port' => 8080,

    /*
     * Providers
     */

    'providers' => [
        // ...
    ],

    /*
     * Middleware
     */

    'middleware' => [
        \App\Http\Middleware\SessionMiddleware::class,
        \App\Http\Middleware\AssetsMiddleware::class,
    ],

    'aliases' => [
        'user_service' => App\Facades\UserServiceFacade::class,
        'auth' => App\Facades\AuthFacade::class,
    ],

    /*
     * Tùy chọn
     */

    'timezone' => 'UTC',
    'locale' => 'en_US',
];
