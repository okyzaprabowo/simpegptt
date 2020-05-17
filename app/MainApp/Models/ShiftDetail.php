<?php

namespace App\MainApp\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftDetail extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shift_detail';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id','created_at'];

    public function shift()
    {
        return $this->belongsTo('App\MainApp\Models\Shift','shift_id','id');
    }
}