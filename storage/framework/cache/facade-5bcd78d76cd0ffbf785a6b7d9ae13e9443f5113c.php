<?php

namespace Facades\App\MainApp\Repositories;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\MainApp\Repositories\Permohonan
 */
class Permohonan extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'App\MainApp\Repositories\Permohonan';
    }
}
