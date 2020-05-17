<?php
// use Facades\App\MainApp\Modules\moduser\Repositories\RoleRepo;
/**
 * Route API feature Auth
 */
$homeSlug = trim(config('AppConfig.client.endpoint.'.config('AppConfig.system.mode').'.home_slug',''),'/');
$groupAuth = [
    'prefix' => str_replace('/'.$homeSlug,'',config('AppConfig.endpoint.api.auth'))
];
Route::group($groupAuth,function(){
    //Auth/LoginController
    Route::post('/login', 'Auth\LoginController@apiLogin')->name('auth.api.login');    

    //Auth/RegisterController
    Route::post('/register', 'Auth\RegisterController@apiRegister')->name('auth.api.register');

    //Auth/ForgotPasswordController
    Route::post('/forgotpassword', 'Auth\ForgotPasswordController@doForgotPassword')->name('auth.api.register');

    //Auth/TokenApiController - generate token akses tanpa user
    // Route::post('/token', 'Auth\TokenApiController@generateToken')->name('auth.api.generatetoken');

    Route::middleware('auth:api')->group(function(){
        //Auth/LoginController
        Route::get('/logout', 'Auth\LoginController@apiLogout')->name('auth.api.logout');
        //TokenApiController
        // Route::post('/token/validate', 'Auth\TokenApiController@validateToken')->name('auth.api.validatetoken');
    });
});

/**
 * Route API feature User Management
 */
$groupUser = [
    'prefix' => str_replace('/'.$homeSlug,'',config('AppConfig.endpoint.api.moduser')),
    // 'middleware' => 'auth:api'
];
Route::get('/notification/setnotif', 'NotificationController@setnotif')->name('user.api.notification.setnotif');
Route::group($groupUser,function(){
    
    /**
     * Module Role
     */
    Route::group(['prefix'=>'role'],function(){ 
        //read list resource
        Route::get('/', 'RoleController@readList')->name('role.api.readList'); 
        //read one resource
        Route::get('/{id}', 'RoleController@readOne')->name('role.api.readOne');

        //create resource
        Route::post('/', 'RoleController@create')->name('role.api.create');  
        //update resource
        Route::put('/{id}', 'RoleController@update')->name('role.api.update'); 
        //delete resource
        Route::delete('/{id}', 'RoleController@delete')->name('role.api.delete');  
    });  

    
    
    /**
     * Module notif
     */
    Route::get('/notification', 'NotificationController@index')->name('user.api.notification');
    Route::get('/notification/type', 'NotificationController@listType')->name('user.api.notification.listType');
    Route::get('/notification/{notificationId}', 'NotificationController@detail')->name('user.api.notification.detail');    
    Route::delete('/notification/{notificationId}', 'NotificationController@deleteNotif')->name('user.api.notification.delete');
    Route::post('/notification/read', 'NotificationController@setRead')->name('user.api.notification.setReadBulk');
    Route::post('/notification/{notificationId}/read', 'NotificationController@setRead')->name('user.api.notification.setRead');
    Route::post('/notification/unread', 'NotificationController@setUnread')->name('user.api.notification.setUnreadBulk');
    Route::post('/notification/{notificationId}/unread', 'NotificationController@setUnread')->name('user.api.notification.setUnread');

    
    /**
     * Feature User
     */
    //read list resource
    Route::get('/', 'UserController@readList')->name('user.api.readList'); 
    //read one resource
    Route::get('/{id}', 'UserController@readOne')->name('user.api.readOne');

    //create resource
    Route::post('/', 'UserController@create')->name('user.api.create');  
    //update resource
    Route::put('/profile', 'UserController@updateProfile')->name('user.api.profile'); 
    Route::put('/{id}', 'UserController@update')->name('user.api.update'); 
    Route::put('/{id}/ban', 'UserController@ban')->name('user.api.ban'); 
    Route::put('/{id}/unban', 'UserController@unban')->name('user.api.unban'); 
    Route::put('/{id}/updatepassword', 'UserController@updatePassword')->name('user.api.updatePassword'); 
    //delete resource
    Route::delete('/{id}', 'UserController@delete')->name('user.api.delete');  
    
});