<?php

namespace App\MainApp\Models;

use Illuminate\Database\Eloquent\Model;

class HariLibur extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hari_libur';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id','created_at'];
}