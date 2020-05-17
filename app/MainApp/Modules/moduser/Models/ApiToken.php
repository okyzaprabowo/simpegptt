<?php

namespace App\MainApp\Modules\moduser\Models;

use Illuminate\Database\Eloquent\Model;

class ApiToken extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'api_tokens';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id','created_at'];
}