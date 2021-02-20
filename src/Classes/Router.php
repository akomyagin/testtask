<?php


namespace App\Classes;

use Symfony\Component\HttpFoundation\Response;

class Router
{
    public $routingMap;

    public $currentController;

    public $currentAction;

    public $currentParam;

    public function __construct()
    {
        $app = Container::getInstance();
        if (isset($app->config['routing'])) {
            $this->routingMap = $app->config['routing'];
        }
    }

    public function getResponse($path)
    {
        $response = new Response();
        if ($this->findController($path)) {
            $controller = new $this->currentController($this->config);
            $response->setStatusCode(Response::HTTP_OK);
            foreach ($controller->headers as $key => $value) {
                $response->headers->set($key, $value);
            }
            $method = $this->currentAction;
            if (is_array($this->currentParam))
                $content = $controller->$method(extract($this->currentParam));
            else
                $content = $controller->$method();
            $response->setContent($content);
        } else {
            $response->setContent('Page not found.');
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            $response->headers->set('content-type', 'text/html');
        }
        return $response;
    }

    protected function findController($path) {
        $this->currentController = '';
        $this->currentAction = '';
        $this->currentParam = '';
        $path = str_replace('\\', '/', $path);
        $arr_path = explode('/', $path);
        $arr_path = array_values(array_diff($arr_path, ['']));
        $full_path = 'App\\Controllers\\';
        if (count($arr_path) == 0 && isset($this->routingMap['defaultController'])
            &&class_exists($full_path.$this->routingMap['defaultController'])) {
            $this->currentController = $full_path.$this->routingMap['defaultController'];
            $this->currentAction = 'indexAction';
            $this->currentParam = '';
            return true;
        }
        if (class_exists($full_path.$arr_path[0].'Controller')) {
            $controller = array_shift($arr_path);
            $this->currentController = $full_path.$controller.'Controller';

        } elseif (isset($this->routingMap['defaultController'])
            &&class_exists($full_path.$this->routingMap['defaultController'])) {
            $this->currentController = $full_path.$this->routingMap['defaultController'];
        }
        if (method_exists($this->currentController, $arr_path[0].'Action')) {
            $method = array_shift($arr_path);
            $this->currentAction = $method.'Action';
        } elseif (isset($controller)) {
            $this->currentAction = 'indexAction';
        } else {
            $this->currentController = '';
            return false;
        }
        $this->currentParam = $arr_path;
        return true;
    }
}