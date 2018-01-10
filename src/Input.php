<?php
namespace oldtailor\oauth;

class Input
{

    public static function get($name, $default = NULL)
    {
        
        return self::input($_GET, $name,$default);
        
    }

    public static function post($name, $default = NULL)
    {
        
        return self::input($_POST, $name,$default);
    }

    public static function input($data, $name, $default = NULL)
    {
        if (isset($data[$name]))
            return $data[$name];
        
        return $data;
    }
}