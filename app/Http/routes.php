<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::auth();

Route::get('/home', 'HomeController@index');

Route::group(['middleware' => 'web'], function () {

    Route::group(['prefix' => 'accounts', 'namespace' => 'Manage'], function () {

        Route::get('', [
            'as'   => 'manage.accounts.index',
            'uses' => 'AccountController@index',
        ]);

    });

    Route::group(['prefix' => 'boards', 'namespace' => 'Manage'], function () {

        Route::get('', [
            'as'   => 'manage.boards.index',
            'uses' => 'BoardController@index',
        ]);
        Route::get('create', [
            'as'   => 'manage.boards.create',
            'uses' => 'BoardController@create',
        ]);
        Route::post('', [
            'as'   => 'manage.boards.store',
            'uses' => 'BoardController@store',
        ]);
        Route::get('{board}', [
            'as'   => 'manage.boards.show',
            'uses' => 'BoardController@show',
        ]);
        Route::get('{board}/edit', [
            'as'   => 'manage.boards.edit',
            'uses' => 'BoardController@edit',
        ]);
        Route::put('{board}', [
            'as'   => 'manage.boards.update',
            'uses' => 'BoardController@update',
        ]);
        Route::delete('{board}', [
            'as'   => 'manage.boards.destroy',
            'uses' => 'BoardController@destroy',
        ]);
    });

});

///////////////////////
// LANGUAGE SWITCHER //
///////////////////////

Route::get('lang/{lang}', ['as' => 'lang.switch', 'middleware' => 'web', 'uses' => 'LanguageController@switchLang']);
