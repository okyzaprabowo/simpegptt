<?php

namespace App\MainApp\Modules\moduser\Models;

class NotificationChannel extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notification_channels';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id','created_at','updated_at'];
    
    /**
     * relasi ke user
     */
    public function user()
    {
        return $this->hasMany('App\MainApp\Modules\moduser\Models\User','id','user_id');
    }
}