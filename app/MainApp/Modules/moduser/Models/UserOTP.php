<?php

namespace App\MainApp\Modules\moduser\Models;

class UserOTP extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_otp';
    const UPDATED_AT = null;
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id','created_at'];
}