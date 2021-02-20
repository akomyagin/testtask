<?php


namespace App\Classes;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class BaseController
{
    public $headers;

    public $twig;

    public function __construct()
    {
        $this->headers['content-type'] = 'text/html';
        $app = Container::getInstance();
        $dir = str_replace('controller', '', basename(strtolower(static::class)));
        if ($dir == 'base') $dir = '';
        $loader = new FilesystemLoader($app->config['base_path'].'/templates/'.$dir);
        $this->twig = new Environment($loader, array(
            'cache' => $app->config['base_path'].'/cache',
            'debud' => true,
        ));
    }

    public function actions()
    {

    }

    public function indexAction()
    {
        return 'Hello, World!';
    }

}