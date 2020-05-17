<?php

namespace App\MainApp\Models;

use Illuminate\Database\Eloquent\Model;

class PegawaiDoktah extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pegawai_doktah';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var arrays
     */
    protected $guarded = ['id','created_at'];
    
    protected $fillable = [
        'pegawai_id',
        'nama',
        'keterangan',
        'filename',
        'filepath'
    ];

    public function pegawai()
    {
        return $this->belongsTo('App\MainApp\Models\Pegawai','pegawai_id','id');
    }
}