<?php

namespace App\MainApp\Modules\moduser\Models;

//use Laravel\Passport\HasApiTokens;//comment jika tidak menggunakan passport
//use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    //comment HasApiTokens jika tidak menggunakan passport
    use NotifiableCustom;//HasApiTokens,

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_idcode','name','username', 'email', 'phone', 'password','auth_password',
        'socialauth_facebook_id','socialauth_facebook_token','socialauth_facebook_data',
        'socialauth_google_id','socialauth_google_token','socialauth_google_data', 'level',
        'note', 'role',  'status', 'banned_note'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'auth_password'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    
    public function profile()
    {
        return $this->hasOne('App\MainApp\Modules\moduser\Models\UserProfile','user_id');
    }    
    public function roles()
    {
        return $this->hasMany('App\MainApp\Modules\moduser\Models\UserRole','user_id');
    }     
    public function userTenant()
    {
        return $this->hasMany('App\MainApp\Modules\moduser\Models\UserTenant','user_id');
    }   
    public function tenant()
    {
        return $this->hasManyThrough(
            'App\Models\Tenant', // table tujuan
            'App\MainApp\Modules\moduser\Models\UserTenant', // table transaksi
            'tenant_id', // Foreign key on table transaksi...
            '', // Foreign key on table tujuan...
            '', // Local key on main model table...
            'user_id' // Local key on table transaksi...
        );
    }    
}
