<?php

namespace App\MainApp\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shift';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id','created_at'];

    public function shiftDetail()
    {
        return $this->hasMany('App\MainApp\Models\ShiftDetail','shift_id','id');
    }
}