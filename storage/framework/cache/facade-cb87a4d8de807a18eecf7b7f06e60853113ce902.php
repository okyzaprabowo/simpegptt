<?php

namespace Facades\App\MainApp\Modules\moduser\Repositories;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\MainApp\Modules\moduser\Repositories\UserRepo
 */
class UserRepo extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'App\MainApp\Modules\moduser\Repositories\UserRepo';
    }
}
