<?php

namespace FashionGuide\Oauth2\Facades;

use Illuminate\Support\Facades\Facade;

class FashionGuide extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'FG';
    }
}
