<?php
use \App\Kernel\App;

date_default_timezone_set('Europe/Copenhagen');
define('CMD', 'python3');
session_start();

require 'kernel.php';

$app = new App(['settings' => require config_path() . '/app.php']);

$app->registerServices();
$app->registerAppMiddlewares();

//Add Access-Control-Allow-Methods header
$app->add(function($request, $response, $next) {
    $route = $request->getAttribute('route');

    $methods = [];

    if (!empty($route)) {
        $pattern = $route->getPattern();

        foreach ($this->router->getRoutes() as $route) {
            if ($pattern === $route->getPattern()) {
                $methods = array_merge_recursive($methods, $route->getMethods());
            }
        }
        //Methods holds all of the HTTP Verbs that a particular route handles.
    } else {
        $methods[] = $request->getMethod();
    }

    $response = $next($request, $response);


    return $response->withHeader('Access-Control-Allow-Methods', implode(",", $methods));
});

require app_path() . '/Routes/routes.php';

//Add Access-Control-Allow-Origin header
$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        //->withHeader('Access-Control-Allow-Origin', 'http://localhost:4200')
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization');
});

$app->run();
