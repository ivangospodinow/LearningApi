<?php

use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

$config = include 'config.php';
$app = new \Slim\App(['settings' => $config]);
$container = $app->getContainer();

// CORS
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
        ->withHeader('Cache-Control', 'public, max_age=3600');
});

$app->get('/', function (Request $request, Response $response, array $args) {
    $response->getBody()->write("hello world");
    return $response;
});

foreach ($config['routes'] as $route) {
    $method = $route['type'];
    $app->$method($route['uri'], function (Request $request, Response $response, array $args) use ($route) {

        $params = $route['type'] === 'get' ? $request->getQueryParams() : $request->getParsedBody();

        if (empty($params)) {
            $params = [];
        }

        $callback = $route['callback'];
        return $response->withJson(call_user_func_array(
            [
                new $callback[0](),
                $callback[1],
            ],
            [$params, $args]
        ));
    });
}

// init factories
foreach ($config['factory'] as $nameKey => $className) {
    $container[$nameKey] = function (Slim\Container $config) use ($className) {
        $instance = new $className();
        return $instance($config->serviceLocator);
    };
}

return $app;
