<?php

namespace Lunantu\AutoPage\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PageController extends Controller
{
    protected $prefix = 'web';

    public function autoPage(Request $request)
    {
        // load route from cache
        $routes = Cache::store('file')->get('auto-page');

        if(!$routes){
            $routes = $this->setCachedRoutes();
        }

        $match = $this->matchRouter($request->path(), $routes);

        // check if route match in cache
        if($match){
            // if route found return view with params
            return view($this->prefix . '.' . $match['view'], $match['params']);
        }

        // make cache route of update dir
        $routes = $this->setCachedRoutes();

        // refind match
        $match = $this->matchRouter($request->path(), $routes);

        if($match){
            // if route found return view with params
            return view($this->prefix . '.' . $match['view'], $match['params']);
        }
        else{
            abort(404);
        }
    }

    // return array of dir contents
    protected function getDirContents($dir, &$results = []) {
        $files = scandir($dir);

        foreach ($files as $key => $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $results[] = $path;
            } else if ($value != "." && $value != "..") {
                $this->getDirContents($path, $results);
                $results[] = $path;
            }
        }

        return $results;
    }

    // return key value array of routes
    protected function getRoutes($dirContents)
    {
        $routes = [];

        foreach($dirContents as $key => $map){
            // local variable
            $view = null;
            $pattern = null;

            // clear path from root disk
            $map = str_replace(resource_path('views/' . $this->prefix), '', $map); // windows
            $map = str_replace(resource_path('views\\' . $this->prefix), '', $map); // unix

            // fix if unix
            $map = str_replace('\\', '/', $map);

            // clear redundant path
            if(!preg_match('/.blade.php/', $map)){
                unset($dirContents[$key]);
                continue;
            }

            // make route pattern
            $pattern = str_replace('index.blade.php', '', $map);
            $pattern = str_replace('.blade.php', '', $pattern);

            // make view path
            $view = str_replace('.blade.php', '', $map);
            $view = str_replace('/', '.', $view);
            $view = ltrim($view, '.');

            $routes[$pattern] = $view;
        }

        // sort array
        ksort($routes);

        return $routes;
    }

    /**
     * function to get match router and params
     * return two value with condition
     * 1. null if not found match router
     * 2. array if found match with values [routes, view, params]
     */
    protected function matchRouter($url, $routes)
    {
        $params = [];
        $matchRoute = false;

        foreach($routes as $route => $view){
            $match = false;
            // change parameter to * for find paramater value
            $routePattern = preg_replace('~{(.*?)}~', '*', $route);

            // make paramater
            $patternSegment = explode('/', rtrim(ltrim($routePattern, '/'), '/'));
            $routeSegment = explode('/', rtrim(ltrim($route, '/'), '/'));
            $pathSegment = explode('/', rtrim(ltrim($url, '/'), '/'));

            // check if leng segment not same
            if(count($routeSegment) != count($pathSegment)){
                continue;
            }

            // check if pattern match with url path and get paramater
            foreach($patternSegment as $key => $segment){
                // make paramater found
                if($segment == '*'){
                    $varName = rtrim(ltrim($routeSegment[$key], '{'), '}');
                    $varValue = $pathSegment[$key];
                    $params[$varName] = $varValue;
                }

                // determine if pattern match with url path
                if($segment != $pathSegment[$key] and $segment != '*'){
                    $params = [];
                    $match = false;
                    break;
                }

                $match = true;
            }

            if($match){
                return [
                    'route' => $route,
                    'view' => $view,
                    'params' => $params
                ];
            }
        }

        return null;
    }

    // make cached fo batter performance
    protected function setCachedRoutes()
    {
        $dirContents = $this->getDirContents(resource_path('views/' . $this->prefix));
        $routes = $this->getRoutes($dirContents);
        Cache::store('file')->put('auto-page', $routes);

        return $routes;
    }
}