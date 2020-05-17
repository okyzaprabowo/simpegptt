<?php

namespace App\MainApp\Models;

use Illuminate\Database\Eloquent\Model;

class Agama extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'agama';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id','created_at'];
}