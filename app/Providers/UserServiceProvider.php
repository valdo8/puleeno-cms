<?php

namespace App\Providers;

use App\Services\UserService;

class UserServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->container->set('user_service', function () {
            return new UserService();
        });
    }

    public function boot()
    {
        // ...
    }
}
