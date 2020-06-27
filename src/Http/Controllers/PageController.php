<?php

namespace Lunantu\AutoPage\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function auto_page(Request $request)
    {
        $prefix = 'web';
        $path = $request->path();

        $view_path = $prefix;

        // check if path not homepage
        if($path != '/'){
            $view_path .= '.' . str_replace('/', '.', $path);
        }

        // check if path is folder
        if(is_dir(resource_path('views/' . $prefix . '/' . $path))){
            $view_path .= '.index';
        }

        // if there view file, show file
        if(view()->exists($view_path)){
            return view($view_path);
        }

        // if file not found abort 404 page
        return abort(404);
    }
}