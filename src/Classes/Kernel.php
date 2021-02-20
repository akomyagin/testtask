<?php


namespace App\Classes;

use Symfony\Component\HttpFoundation\Request;

class Kernel
{
    public function __construct(array $config)
    {
        if (!isset($config['base_path'])) $config['base_path'] = dirname(__DIR__);
        $app = Container::getInstance();
        $app->config = $config;
        if (isset($config['bootstrap'])) {
            foreach ($config['bootstrap'] as $key => $value) {
                if($obj = $app->loadClasses($value)) {
                    $app->container[$key] = $obj;
                };
            }
        }
    }

    public function run()
    {
        $request = Request::createFromGlobals();
        $app = Container::getInstance();
        $app->container['request'] = $request;
        $path = $request->getPathInfo();
        $router = new Router();
        $response = $router->getResponse($path);
        $response->prepare($request);
        $response->send();
    }
}