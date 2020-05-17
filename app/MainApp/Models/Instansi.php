<?php

namespace App\MainApp\Models;

use Illuminate\Database\Eloquent\Model;

class Instansi extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'instansi';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id','created_at','updated_at'];
    protected $fillable = [
        'kode',
        'nama',
        'eselon',
        'singkatan',
        'induk',
        'kelurahan_id',
        'induk_path'
    ];
}