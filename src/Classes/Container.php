<?php


namespace App\Classes;

use App\Classes\DB\Connection;
use Symfony\Component\HttpFoundation\Request;
class Container
{
    public  $config;
    public $container;
    private static $instances = [];
    /**
     * @var Connection
     */
   // public $db;
    /**
     * @var \http\Env\Request
     */
   // public $request;

    protected function __construct() { }

    protected function __clone() { }

    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    /**
     * @return Container
     */
    public static function getInstance(): Container
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static();
        }
        return self::$instances[$cls];
    }

    public function __get($property)
    {
        if (isset($this->container[$property])) return $this->container[$property];
        else throw new \Exception('Class not found.');
    }

    public function loadClasses(array $config)
    {
        if (isset($config['class'])) {
            $obj = new $config['class']($config['param']);
            return $obj;
        } else {
            return false;
        }
    }
}