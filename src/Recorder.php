<?php
namespace oldtailor\oauth;

class Recorder
{

    private static $data;

    private $inc;

    private $error;
    
    private $key = "oldtailor_oauth";
    
    public function __construct()
    {
        session_start();
        
        $this->error = new ErrorCase();
        
        self::$data = empty($_SESSION[$this->key]) ? array() : $_SESSION[$this->key];
    }

    public function write($name, $value)
    {           
        self::$data[$name] = $value;
        $this->save();
    }

    public function read($name)
    {
        if (empty(self::$data[$name])) {
            return null;
        } else {
            return self::$data[$name];
        }
    }
    
    public function delete($name)
    {
        unset(self::$data[$name]);
        $this->save();
    }

    private function save()
    {
        $_SESSION[$this->key] = self::$data;
    }
    
}
