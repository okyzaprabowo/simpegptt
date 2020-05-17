<?php

namespace App\MainApp\Modules\moduser\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'password_resets';
    protected  $primaryKey  = 'email';
    const UPDATED_AT = null;
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['created_at'];
}