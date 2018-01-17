<?php
namespace oldtailor\oauth;

class Config {
    
    private static $cfg = [];
    
    public static function get($grant_type){
        
        return empty(static::$cfg[$grant_type]) ? [] : static::$cfg[$grant_type];
    }
    
    public static function set($grant_type,$cfg){
        
        static::$cfg[$grant_type] = $cfg;    
    }
    
    
}