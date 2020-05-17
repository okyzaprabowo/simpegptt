<?php

namespace App\MainApp\Models;

use Illuminate\Database\Eloquent\Model;

class PegawaiKeluarga extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pegawai_keluarga';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var arrays
     */
    protected $guarded = ['id','created_at'];
    
    protected $fillable = [
        'pegawai_id',
        'nama',
        'kelamin',
        'tanggal_lahir',
        'tempat_lahir',
        'relasi'
    ];

    public function pegawai()
    {
        return $this->belongsTo('App\MainApp\Models\Pegawai','pegawai_id','id');
    }
}