<?php

namespace App\MainApp\Models;

use Illuminate\Database\Eloquent\Model;

class JabatanInstansi extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'jabatan_instansi';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id','created_at'];
   
    public function instansi()
    {
        return $this->belongsTo('App\MainApp\Models\Instansi','instansi_id','id');
    }
 

     
}