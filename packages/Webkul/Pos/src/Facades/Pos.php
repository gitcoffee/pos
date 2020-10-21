<?php

namespace Webkul\Pos\Facades;

use Illuminate\Support\Facades\Facade;

class Pos extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'pos';
    }
}