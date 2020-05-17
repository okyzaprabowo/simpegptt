<?php

namespace App\MainApp\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pegawai';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var arrays
     */
    protected $guarded = ['id','created_at'];
    protected $fillable = [
        'user_id',
        'kode',
        'nama',
        'instansi_id',
        'instansi_induk_path',
        'jabatan_id',
        'gelar_depan',
        'gelar_belakang',
        'ktp',
        'npwp',
        'tanggal_lahir',
        'tempat_lahir',
        'agama_id',
        'kelamin',
        'golongan_darah',
        'status_kawin_id',
        'tipe',
        'foto'
    ];

    public function instansi()
    {
        return $this->hasOne('App\MainApp\Models\Instansi','id','instansi_id');
    }
    
    public function absensi()
    {
        return $this->hasMany('App\MainApp\Models\Absensi','pegawai_id','id');
    }

    public function jabatan()
    {
        return $this->hasOne('App\MainApp\Models\Jabatan','id','jabatan_id');
    }
    
    public function agama()
    {
        return $this->hasOne('App\MainApp\Models\Agama','id','agama_id');
    }

    public function statusKawin()
    {
        return $this->hasOne('App\MainApp\Models\StatusKawin','id','status_kawin_id');
    }
    public function user()
    {
        return $this->hasOne('App\MainApp\Modules\moduser\Models\User','id','user_id');
    }

    public function alamat()
    {
        return $this->hasMany('App\MainApp\Models\PegawaiAlamat','pegawai_id','id');
    }
    public function keluarga()
    {
        return $this->hasMany('App\MainApp\Models\PegawaiKeluarga','pegawai_id','id');
    }
    public function pendidikan()
    {
        return $this->hasMany('App\MainApp\Models\PegawaiPendidikan','pegawai_id','id');
    }
    public function doktah()
    {
        return $this->hasMany('App\MainApp\Models\PegawaiDoktah','pegawai_id','id');
    }
}