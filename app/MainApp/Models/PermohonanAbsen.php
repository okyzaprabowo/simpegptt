<?php

namespace App\MainApp\Models;

use Illuminate\Database\Eloquent\Model;

class PermohonanAbsen extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'permohonan_absen';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var arrays
     */
    protected $guarded = ['id','created_at'];

    public function pegawai()
    {
        // return $this->belongsTo('App\MainApp\Models\Pegawai','pegawai_id','id');
        return $this->hasOne('App\MainApp\Models\Pegawai','id','pegawai_id');
    }

    public function jenisIjin()
    {
        return $this->hasOne('App\MainApp\Models\JenisIjin','id','ijin_id');
    }
}