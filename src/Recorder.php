<?php
namespace oldtailor\oauth;

class Recorder
{

    private $data;

    private $key = "oldtailor_oauth";

    public function __construct()
    {
        $this->data = empty($_SESSION[$this->key]) ? array() : $_SESSION[$this->key];
    }

    public function write($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function read($name)
    {
        if (empty($this->data[$name])) {
            return null;
        } else {
            return $this->data[$name];
        }
    }

    public function delete($name)
    {
        unset($this->data[$name]);
    }

    public function __construct()
    {
        $_SESSION[$this->key] = $this->data;
    }
    
}
