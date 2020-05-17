<?php

$homeSlug = trim(config('AppConfig.client.endpoint.'.config('AppConfig.system.mode').'.home_slug',''),'/');
// $routeOpt = route_web_opt(config('bssystem.url.ac'),config('bssystem.url.acapi'));

Route::group([], function() use($homeSlug) {
    
    //VerificationController
    Route::get('/emailverify', 'Auth\VerificationController@verify')->name('auth.emailVerification');
    Route::get('/emailverify/success', 'Auth\VerificationController@verifySuccess')->name('auth.emailVerification.success');
    Route::get('/emailverify/fail', 'Auth\VerificationController@verifyFail')->name('auth.emailVerification.fail');


//         Route::get('/testing', function($apps_code) {
//             dd(UserRepo::generateUserIdcode());
//             dd(AppsClient::first()->toArray());
//         })->name('auth.testing');

    Route::group(['prefix'=>str_replace('/'.$homeSlug,'',config('AppConfig.endpoint.admin.auth'))], function(){

        //LoginController 
        Route::get('/login', 'Auth\LoginController@login')->name('auth.login');
        Route::post('/login', 'Auth\LoginController@doLogin');
        Route::get('/logout', 'Auth\LoginController@logout')->name('auth.logout');

        // //RegisterController
        Route::get('/register', 'Auth\RegisterController@register')->middleware('AppsPermissionCheck')->name('auth.register');
        Route::post('/register', 'Auth\RegisterController@doRegister')->middleware('AppsPermissionCheck')->name('auth.doRegister');

        // //ForgotPassowrdController
        Route::get('/forgotpassword', 'Auth\ForgotPasswordController@forgotPassword')->name('auth.forgotPassword');//form forgot password
        Route::post('/forgotpassword', 'Auth\ForgotPasswordController@doForgotPassword'); //send reset email

        // //ResetPasswordController
        Route::get('/resetpassword', 'Auth\ResetPasswordController@resetPassword')->name('auth.resetPassword'); //form reset password dari link yg didapat di email
        Route::post('/resetpassword', 'Auth\ResetPasswordController@doResetPassword');//prosess reset password
        Route::get('/resetpassword/fail', 'Auth\ResetPasswordController@verifyFail')->name('auth.resetPassword.fail');
    });

    //Social Sign On
    Route::get('/socialauth/{provider}', 'Auth\SocialSignOnController@redirectToProvider')->name('socialAuth.login');
    // //Social Sign On
    Route::get('/socialauth/{provider}/callback', 'Auth\SocialSignOnController@handleProviderCallback')->name('socialAuth.callback');

    
    Route::group([
        'prefix'=>str_replace('/'.$homeSlug,'',config('AppConfig.endpoint.admin.moduser')),
        'middleware' => 'auth'
    ], function(){

        //profile resource
        Route::get('/profile', 'UserController@profile')->name('user.profile'); 
        Route::put('/profile', 'UserController@updateProfile')->name('user.profile');
        //ubah role user yang sedang loign
        Route::get('/change_role/{role_code}', 'UserController@changeRole')->name('user.changerole'); 

        //read list resource
        Route::get('/', 'UserController@readList')->name('user.list'); 
        //form add
        Route::get('/add', 'UserController@addNew')->name('user.addNew'); 
        //read one resource
        Route::get('/{id}', 'UserController@readOne')->name('user.edit'); 
        //view resource
        Route::get('/{id}/view', 'UserController@view')->name('user.view');   
        //create resource
        Route::post('/', 'UserController@create')->name('user.create');  
        //update resource 
        Route::put('/{id}', 'UserController@update')->name('user.update'); 
        Route::put('/{id}/ban', 'UserController@ban')->name('user.ban'); 
        Route::put('/{id}/unban', 'UserController@unban')->name('user.unban'); 
        Route::put('/{id}/updatepassword', 'UserController@updatePassword')->name('user.updatePassword'); 
        //delete resource
        Route::delete('/{id}', 'UserController@delete')->name('user.delete');  

        
        Route::group(['prefix'=>'role'],function(){  
            Route::get('/', 'RoleController@index')->name('role.list');
            //form add
            Route::get('/add', 'RoleController@addNew')->name('role.addNew');
            Route::post('/', 'RoleController@create')->name('role.create');
            Route::get('/{id}', 'RoleController@edit')->name('role.edit');
            Route::put('/{id}', 'RoleController@update')->name('role.update');
            Route::delete('/{id}', 'RoleController@delete')->name('role.delete');
        });
    });
});