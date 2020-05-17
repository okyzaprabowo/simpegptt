<?php

namespace App\MainApp\Modules\moduser\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_profiles';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id','created_at','updated_at'];
    // protected $fillable = [
    //     'user_id',
    //     'avatar',
    //     'gender',
    //     'date_of_birth',
    //     'socnet_facebook',
    //     'socnet_instagram',
    //     'address',
    //     'postal_code'
    //     ];
}