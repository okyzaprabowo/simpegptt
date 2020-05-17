<?php

namespace App\MainApp\Models;

use Illuminate\Database\Eloquent\Model;

class PegawaiAlamat extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pegawai_alamat';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var arrays
     */
    protected $guarded = ['id','created_at'];
    protected $fillable = [
        'pegawai_id',
        'tipe_alamat',
        'alamat',
        'kelurahan',
        'kecamatan',
        'kota',
        'provinsi',
        'kodepos',
        'telepon',
        'ponsel',
        'email',
        'emer_nama',
        'emer_pekerjaan',
        'emer_relasi'
    ];

    public function pegawai()
    {
        return $this->belongsTo('App\MainApp\Models\Pegawai','pegawai_id','id');
    }
}