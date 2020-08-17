<?php

namespace Lunantu\AutoPage\Helpers;

use Lunantu\AutoPage\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route as LaravelRoute;

class Route
{
    public function route()
    {
        LaravelRoute::get('{slug}', [PageController::class, 'autoPage'])
            ->where('slug', '.*');
    }
}
