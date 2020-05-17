<?php

namespace App\MainApp\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiRawUpload extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'absensi_raw_uploads';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id','created_at'];
    
    public function absensiRaw()
    {
        return $this->hasMany('App\MainApp\Models\AbsensiRaw','absensi_raw_upload_id','id');
    }

    public function mesinAbsen()
    {
        return $this->hasOne('App\MainApp\Models\MesinAbsen','id','mesin_absen_id');
    }
}