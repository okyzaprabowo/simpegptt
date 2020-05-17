<?php

namespace App\MainApp\Models;

use Illuminate\Database\Eloquent\Model;

class StatusKawin extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'status_kawin';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id','created_at'];
}