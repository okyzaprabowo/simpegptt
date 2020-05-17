<?php

namespace Facades\App\MainApp\Modules\moduser\Services;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\MainApp\Modules\moduser\Services\UserAuth
 */
class UserAuth extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'App\MainApp\Modules\moduser\Services\UserAuth';
    }
}
