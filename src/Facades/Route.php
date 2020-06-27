<?php

namespace Lunantu\AutoPage\Facades;

use Illuminate\Support\Facades\Facade;

class Route extends Facade
{
    protected static function getFacadeAccessor(){
        return 'auto_page_router';
    }
}
