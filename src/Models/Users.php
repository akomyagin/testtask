<?php


namespace App\Models;

use App\Classes\Container;

class Users
{
    public $email_hash;
    public $phone_crypt;
    public $id;
    private $email;
    private $phone;
    public $error;

    public function __construct()
    {
        $this->error = [];
    }

    public function findModel($email_hash)
    {
        $app = Container::getInstance();
        $row = $app->db->getRows("select * from users where email = '$email_hash'");
        if ($row) {
            $this->email_hash = $row[0]['email'];
            $this->phone_crypt = $row[0]['phone'];
            $this->id = $row[0]['id'];
            return $this;
        }
        return false;
    }

    public function AddOrUpdate($email_hash = '', $phone_crypt = '')
    {
        $email_hash = (strlen($email_hash) > 0) ? $email_hash : $this->email_hash;
        $phone_crypt = (strlen($phone_crypt) > 0) ? $phone_crypt : $this->phone_crypt;
        $app = Container::getInstance();
        if ($this->findModel($email_hash)) {
            $app->db->exec("update users set phone = '$phone_crypt' where email = '$email_hash'");
        } else {
            $app->db->exec("insert into users (phone, email) values ('$phone_crypt','$email_hash')");
        }
    }

    public function validateEmail($email)
    {
        $this->error = [];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
           $this->error[] = 'Incorrect e-mail';
        }
        if (empty($email)){
            $this->error[] = 'Enter email';
        }
        return count($this->error) == 0;
    }

    public function validatePhone($phone)
    {
        $this->error = [];
        if (empty($phone)){
            $this->error[] = 'Enter phone';
        }
        return count($this->error) == 0;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        $this->email_hash = md5($this->email);
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
        $this->phone_crypt = self::cryptPhone($this->phone, $this->email);
    }

    public static function cryptPhone($phone, $email)
    {
        $key = self::getKey($email);
        return base64_encode($phone);
        //return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $phone, MCRYPT_MODE_CBC));
    }

    public static function decryptPhone($crypt_phone, $email)
    {
        $key = self::getKey($email);
        return base64_decode($crypt_phone);
        //return mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, base64_decode($crypt_phone), MCRYPT_MODE_CBC);
    }

    public static function getKey($email_hash)
    {
        $app = Container::getInstance();
        $app_key = $app->config['app_key'];
        $key = md5($email_hash.md5($app_key.$email_hash)).$email_hash;
        $key = md5($key);
        return $key;
    }

    public function sendEmail()
    {
        $result = false;
        $headers = 'From: no-reply@domain.ru' . "\r\n" .
            'Reply-To: no-reply@domain.ru' . "\r\n" .
            'X-Mailer: Mailer';
        if (strlen($this->email) > 0 && $this->validateEmail($this->email)) {
            $decryptPhone = $this::decryptPhone($this->phone_crypt, $this->email);
            $result = mail($this->email, 'Your phone', 'Your phone: '. $decryptPhone, $headers);
        }
        return $result;
    }
}