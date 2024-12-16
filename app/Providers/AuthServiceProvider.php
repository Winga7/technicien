<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->configurePolicies();
    }

    protected function configurePolicies()
    {
        $this->app->bind(UserPolicy::class, function () {
            return new UserPolicy();
        });

        $this->app->bind(User::class, function () {
            return new UserPolicy();
        });
    }
}
