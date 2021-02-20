<?php


namespace App\Classes\DB;

use PDO;

class Connection
{
    public $db;

    public $param;

    public function __construct($config)
    {
        try
        {
            $this->db = new PDO(
                $config['dsn'].';charset='.$config['charset'],
                $config['username'],
                $config['password'],
                array(PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES UTF8')
            );
        }
        catch (PDOException $e)
        {
            throw new \Exception('Error db connection.');
        }
    }

    public function prepare(string $sql)
    {
        return $this->db->prepare($sql);
    }

    public function exec(string $sql)
    {
        return $this->db->exec($sql);
    }

    public function query(string $sql, array $args=[])
    {
        try {
            if (!$args) return $this->db->query($sql);
            $stmt = $this->prepare($sql);
            if (count($args) > 0) {
                $stmt->execute($args);
            } else {
                $stmt->execute();
            }
            return $stmt;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getRows(string $sql, array $args = [])
    {
        return $this->query($sql, $args)->fetchAll();
    }
}