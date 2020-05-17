<?php

namespace App\MainApp\Models;

use Illuminate\Database\Eloquent\Model;

class PegawaiPendidikan extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pegawai_pendidikan';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var arrays
     */
    protected $guarded = ['id','created_at'];
    
    protected $fillable = [
        'pegawai_id',
        'nama_sekolah',
        'is_formal',
        'tingkat',
        'program_studi',
        'tanggal_masuk',
        'tanggal_lulus'
    ];

    public function pegawai()
    {
        return $this->belongsTo('App\MainApp\Models\Pegawai','pegawai_id','id');
    }
}