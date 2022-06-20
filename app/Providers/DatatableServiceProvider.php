<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class DatatableServiceProvider extends ServiceProvider
{

    public static $providers = [
        'customers' => 'App\Repositories\Datatable\CustomerRepository'
    ];

    public function register()
    {
        foreach(self::$providers as $key => $name) {
            $this->app->singleton($name, $name);
        }
    }
}