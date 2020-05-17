<?php

namespace App\MainApp\Models;

use Illuminate\Database\Eloquent\Model;

class MesinAbsen extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mesin_absen';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id','created_at'];

    protected $fillable = ['nama','ip','instansi_id'];

    public function instansi()
    {
        return $this->hasOne('App\MainApp\Models\Instansi','id','instansi_id');
    }
}