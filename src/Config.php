<?php
namespace oldtailor\oauth;

/**
 * oauth2 配置
 *
 * @author metooweb
 * @property string $name 配置名
 * @property string $client_id
 * @property string $client_secret
 * @property string $grant_type
 * @property string $scope
 * @property string $redirect_uri
 * @property string $recorder_key
 */
class Config
{

    private $data = [];

    private static $pool = [];

    public function __construct($data=[]){
        
        $this->data = $data;
    
    }
    
    public static function init($data=[]){
        
        $cfg = new static($data = []);
        static::$pool[$cfg->name] = $cfg;
        return $cfg;
    }
    
    public static function set(Config $cfg)
    {
        static::$pool[$cfg->name] = $cfg;
    }

    /**
     * 获取配置
     * @param string $name
     * @return NULL|Config
     */
    public static function get($name)
    {
        return empty(static::$pool[$name]) ? null : static::$pool[$name];
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function __get($name)
    {
        return empty($this->data[$name]) ? null : $this->data[$name];
    }
}