<?php

namespace App\MainApp\Models;

use Illuminate\Database\Eloquent\Model;

class JenisIjinKategori extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'jenis_ijin_kategori';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id','created_at'];
    public function jenisIjin()
    {
        return $this->hasMany('App\MainApp\Models\JenisIjin','jenis_ijin_kategori_id', 'id');
    }
}