<?php

namespace App\MainApp\Models;

use Illuminate\Database\Eloquent\Model;

class JenisIjin extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'jenis_ijin';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id','created_at'];
    protected $fillable = ['nama','deskripsi','singkatan','warna','template_keterangan','batas_ijin','batas_ijin_tahunan','is_periode','jenis_ijin_kategori_id','is_show_scanner'];
    
    public function kategori()
    {
        return $this->hasOne('App\MainApp\Models\JenisIjinKategori','id','jenis_ijin_kategori_id');
    }
}