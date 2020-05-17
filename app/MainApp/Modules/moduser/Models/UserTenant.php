<?php

namespace App\MainApp\Modules\moduser\Models;

use Illuminate\Database\Eloquent\Model;

class UserTenant extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_tenants';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id','created_at','updated_at'];
    
    public function user()
    {
        return $this->belongsTo('App\MainApp\Modules\moduser\Models\User','user_id');
    }  

    public function tenant()
    {
        return $this->belongsTo('App\Models\Tenant','tenant_id');
    }   
}