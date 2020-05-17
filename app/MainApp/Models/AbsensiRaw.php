<?php

namespace App\MainApp\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiRaw extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'absensi_raw';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id','created_at'];
    
    public function pegawai()
    {
        return $this->hasOne('App\MainApp\Models\Pegawai','kode','pin');
    }
    public function absensiRawUpload()
    {
        return $this->belongsTo('App\MainApp\Models\AbsensiRawUpload','absensi_raw_upload_id','id');
    }
}