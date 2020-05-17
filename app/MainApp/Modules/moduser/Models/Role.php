<?php

namespace App\MainApp\Modules\moduser\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'roles';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id','created_at','updated_at'];
    
    protected $casts = [
        'rule' => 'array'
    ];
    
    public function tenantGroup()
    {
        return $this->belongsTo('App\Models\TenantGroup','tenant_group_id');
    } 
    public function tenant()
    {
        return $this->belongsTo('App\Models\Tenant','tenant_id');
    }   
}