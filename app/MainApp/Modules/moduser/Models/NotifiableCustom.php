<?php

namespace App\MainApp\Modules\moduser\Models;

use Illuminate\Notifications\Notifiable as BaseNotifiable;

trait NotifiableCustom
{
    use BaseNotifiable;

    /**
     * Get the entity's notifications.
     */
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable')
                            ->orderBy('created_at', 'desc');
    }
}