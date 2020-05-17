<?php

namespace App\MainApp\Modules\moduser\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_roles';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id','created_at','updated_at'];
    
    public function role()
    {
        return $this->belongsTo('App\MainApp\Modules\moduser\Models\Role','role_id');
    }   
}