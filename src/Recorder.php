<?php
namespace oldtailor\oauth;

class Recorder
{

    private static $data;

    private $inc;

    private $error;

    public function __construct()
    {
        session_start();
        
        $this->error = new ErrorCase();
        
        self::$data = empty($_SESSION['oldtailor_oauth']) ? array() : $_SESSION['oldtailor_oauth'];
    }

    public function write($name, $value)
    {
        self::$data[$name] = $value;
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
    }

    function __destruct()
    {
        $_SESSION['oldtailor_oauth'] = self::$data;
    }
}
