<?php
namespace App\Facades;

use Core\Facade;

class Cache extends Facade{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'cache';
    }

}