<?php

use Illuminate\Support\Facades\Route;
use App\Repositories\CustomerRepository;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::redirect('/', 'customers');

Route::get('/customers', function () {
    return view('customers');
});

Route::get('/test', function () {

    $providers = [
        'customers' => 'App\Repositories\CustomerRepository'
    ];

    $n = 'App\Repositories\CustomerRepository';

    foreach($providers as $key => $name) {
        dd($key);
    }

    $sortDirection = app($n)->sortDirection();
    $sortBy = app($n)->sortBy();
    $query = app(CustomerRepository::class)->query();
    $columns = app(CustomerRepository::class)->columns();
    dd($sortBy, $sortDirection, $query, $columns);
});

