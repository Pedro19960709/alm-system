<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect(route('getLogin'));
});

Route::namespace('App\\Http\\Controllers')->group(function () {
    Route::namespace('Auth')->group(function () {
        Route::get('/login', 'LoginController@getIndex')->name('getLogin');
        Route::post('/login', 'LoginController@login')->name('postLogin');
    });

    Route::middleware(['auth', 'admin.menu'])->group(function () {
        Route::namespace('Auth')->group(function () {
            Route::post('/logout', 'LogoutController@logout')->name('getLogout');
        });

        Route::namespace('Admin')->group(function () {        
            Route::get('/dashboard', 'HomeController@getIndex')->name('home');
            
            Route::middleware(['user.type'])->group(function () {
                //USERS MODULE
                Route::get('/users/index', 'UserController@getIndex')->name('getUserIndex');
                Route::post('/users/rows', 'UserController@postRows')->name('postRowsUser');
                Route::get('/users/add', 'UserController@getAdd')->name('addUser');
                Route::post('/users/add', 'UserController@postAdd');
                Route::get('/users/edit/{id}', 'UserController@getEdit');
                Route::post('/users/edit/{id}', 'UserController@postEdit');
                Route::get('/users/delete/{id}', 'UserController@getDelete');

                //AREAS MODULE
                Route::get('/areas/index', 'AreaController@getIndex')->name('getAreaIndex');
                Route::post('/areas/rows', 'AreaController@postRows')->name('postRowsArea');
                Route::get('/areas/add', 'AreaController@getAdd')->name('addArea');
                Route::post('/areas/add', 'AreaController@postAdd');
                Route::get('/areas/edit/{id}', 'AreaController@getEdit');
                Route::post('/areas/edit/{id}', 'AreaController@postEdit');
                Route::get('/areas/delete/{id}', 'AreaController@getDelete');

                //DEPARTMENTS MODULE
                Route::get('/departments/index', 'DepartmentController@getIndex')->name('getDepartmentIndex');
                Route::post('/departments/rows', 'DepartmentController@postRows')->name('postRowsDepartment');
                Route::get('/departments/add', 'DepartmentController@getAdd')->name('addDepartment');
                Route::post('/departments/add', 'DepartmentController@postAdd');
                Route::get('/departments/edit/{id}', 'DepartmentController@getEdit');
                Route::post('/departments/edit/{id}', 'DepartmentController@postEdit');
                Route::get('/departments/delete/{id}', 'DepartmentController@getDelete');

                //ARTICULO MODULE
                Route::get('/articles/index', 'ArticleController@getIndex')->name('getArticlesIndex');
                Route::post('/articles/rows', 'ArticleController@postRows')->name('postRowsArticles');
                Route::get('/articles/add', 'ArticleController@getAdd');
                Route::post('/articles/add', 'ArticleController@postAdd');
                Route::get('/articles/edit/{id}', 'ArticleController@getEdit');
                Route::post('/articles/edit/{id}', 'ArticleController@postEdit');
                Route::get('/articles/delete/{id}', 'ArticleController@getDelete');            

                //PETITIONS MODULE
                Route::get('/petitions/item-deliver/{id}', 'PetitionController@getItemDeliver');
                Route::post('/petitions/item-deliver/{id}', 'PetitionController@postItemDeliver')->name('postItemDeliver');
                Route::get('/petitions/cancel/{id}', 'PetitionController@cancelPetition');
                Route::get('/petitions/download-pdf/{id}', 'PetitionController@generatePDF');
                Route::get('/test-mail', 'PetitionController@sendMail');
            });

            //PETITIONS MODULE
            Route::get('/petitions/index', 'PetitionController@getIndex')->name('getPetitionIndex');
            Route::post('/petitions/rows', 'PetitionController@postRows')->name('postRowsPetition');
            Route::get('/petitions/add', 'PetitionController@getAdd')->name('addPetition');
            Route::post('/petitions/add', 'PetitionController@postAdd');
            Route::get('/petitions/item-history/{id}', 'PetitionController@getHistory');
        });
    });
});