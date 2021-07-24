<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'type'], function () {
    Route::get('/', 'TypeController@index');
    Route::get('/{id}', 'TypeController@show');
    Route::group(['middleware' => ['auth:api', 'role:admin']], function () {
        Route::post('/', 'TypeController@store');
        Route::put('/{id}', 'TypeController@update');
    });
});

Route::group(['prefix' => 'country'], function () {
    Route::get('/', 'CountryController@index');
    Route::get('/{id}', 'CountryController@show');
    Route::group(['middleware' => ['auth:api', 'role:admin']], function () {
        Route::post('/', 'CountryController@store');
        Route::put('/{id}', 'CountryController@update');
    });
});

Route::group(['prefix' => 'province'], function () {
    Route::get('/', 'ProvinceController@index');
    Route::get('/{id}', 'ProvinceController@show');
    Route::group(['middleware' => ['auth:api', 'role:admin']], function () {
        Route::post('/', 'ProvinceController@store');
        Route::put('/{id}', 'ProvinceController@update');
        Route::delete('/{id}', 'ProvinceController@destroy');
    });
});

Route::group(['prefix' => 'district'], function () {
    Route::get('/', 'DistrictController@index');
    Route::get('/{id}', 'DistrictController@show');
    Route::group(['middleware' => ['auth:api', 'role:admin']], function () {
        Route::post('/', 'DistrictController@store');
        Route::put('/{id}', 'DistrictController@update');
        Route::delete('/{id}', 'DistrictController@destroy');
    });
});

Route::group(['prefix' => 'city'], function () {
    Route::get('/', 'CityController@index');
    Route::get('/{id}', 'CityController@show');
    Route::group(['middleware' => ['auth:api', 'role:admin']], function () {
        Route::post('/', 'CityController@store');
        Route::put('/{id}', 'CityController@update');
        Route::delete('/{id}', 'CityController@destroy');
    });
});

Route::group(['prefix' => 'supplier'], function () {
    Route::get('/', 'SupplierController@index');
    Route::get('/{id}', 'SupplierController@show');
    Route::group(['middleware' => ['auth:api']], function () {
        Route::post('/{id}/address', 'SupplierController@storeAddress');
        Route::post('/{id}/stock', 'SupplierController@storeStock');
        Route::put('/{id}', 'SupplierController@update');
        Route::group(['middleware' => ['role:agent']], function () {
            Route::post('/{id}/order', 'SupplierController@order');
        });
        Route::group(['middleware' => ['role:admin']], function () {
            Route::post('/', 'SupplierController@store');
            Route::delete('/{id}', 'SupplierController@destroy');
        });
    });
});

Route::group(['prefix' => 'agent'], function () {
    Route::get('/', 'AgentController@index');
    Route::get('/{id}', 'AgentController@show');
    Route::group(['middleware' => ['auth:api']], function () {
        Route::post('/{id}/address', 'AgentController@storeAddress');
        Route::put('/{id}', 'AgentController@update');
        Route::group(['middleware' => ['role:admin|supplier']], function () {
            Route::post('/', 'AgentController@store');
            Route::delete('/{id}', 'AgentController@destroy');
        });
    });
});

Route::group(['prefix' => 'product'], function () {
    Route::get('/', 'ProductController@index');
    Route::get('/{id}', 'ProductController@show');
    Route::group(['middleware' => ['auth:api']], function () {
        Route::group(['middleware' => ['role:admin|supplier']], function () {
            Route::post('/', 'ProductController@store');
            Route::post('/{id}', 'ProductController@update');
            Route::delete('/{id}', 'ProductController@destroy');
        });
    });
});

Route::group(['prefix' => 'order'], function () {
    Route::get('/', 'OrderController@index');
    Route::get('/{id}', 'OrderController@show');
    Route::group(['middleware' => ['auth:api']], function () {
        Route::post('/{id}/status', 'OrderController@storeStatus');
        Route::group(['middleware' => ['role:agent']], function () {
            Route::post('/{id}/payment', 'OrderController@storePayment');
        });
        Route::group(['middleware' => ['role:supplier']], function () {
            Route::post('/{id}/payment/verify', 'OrderController@verifyPayment');
        });
    });
});

Route::group(['prefix' => 'near-me'], function () {
    Route::get('agent', 'AgentController@showNearMe');
});

Route::group(['prefix' => 'oauth'], function () {
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@signup');
    Route::group(['middleware' => 'auth:api'], function() {
        Route::delete('/revoke', 'AuthController@revokeUserToken');
        Route::get('/info', 'AuthController@checkAuth');
    });
    Route::group(['middleware' => 'client.credentials'], function() {
        Route::get('/client/info', 'AuthController@checkAuth');
    });
});

\Laravel\Passport\Passport::routes(function ($router) {
    Route::post('/token', [
        'uses' => 'AccessTokenController@issueToken',
        'as' => 'api.oauth.token',
    ]);
    Route::get('/scopes', [
        'uses' => 'ScopeController@all',
        'as' => 'api.oauth.scopes.index',
    ]);
});

//Route::post('password/reset', 'Auth\ResetPasswordController@reset');

Route::group(['prefix' => 'email/verify'], function () {
    Route::get('/{id}', 'EmailVerificationController@verify')->name('verification.verify');
    Route::post('/{value}/resend', 'EmailVerificationController@resend')->name('verification.resend');
});

Route::group(['prefix' => 'me', 'middleware' => 'auth:api'], function () {
    Route::get('user', 'UserController@showMyData');
    Route::put('user', 'UserController@updateMyData');
    Route::get('order', 'OrderController@showMyOrders');
    Route::post('user/password/create', 'UserController@createPassword');
    Route::post('user/password/change', 'UserController@changePassword');
});

Route::group(['prefix' => 'user'], function () {
    Route::post('/', 'UserController@store');
    Route::group(['middleware' => ['auth:api', 'role:admin']], function () {
        Route::get('/', 'UserController@index');
        Route::delete('/{id}', 'UserController@destroy');
    });
});
