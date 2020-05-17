<?php

namespace App\MainApp\Models;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'jabatan';
    
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

    public function jabatanInstansi(){
        return $this->hasMany('App\MainApp\Models\JabatanInstansi','jabatan_id','id');
    }

     
}