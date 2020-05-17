<?php

namespace App\MainApp\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'absensi';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id','created_at'];
    
    public function pegawai()
    {
        return $this->hasOne('App\MainApp\Models\Pegawai','id','pegawai_id');
    }
    public function jenisIjin()
    {
        return $this->hasOne('App\MainApp\Models\JenisIjin','id','jenis_ijin_id');
    }

    public function shift()
    {
        return $this->hasOne('App\MainApp\Models\Shift','id','shift_id');
    }
}