<?php

namespace Dartmoon\TabManager\Facades;

use Dartmoon\TabManager\TabManager as TabManagerTabManager;

class TabManager
{
    public static function __callStatic($name, $arguments)
    {
        $service = new TabManagerTabManager;
        return call_user_func_array([$service, $name], $arguments);
    }
}