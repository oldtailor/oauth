<?php
namespace oldtailor\oauth\GrantType;

use oldtailor\oauth\Recorder;
use oldtailor\oauth\Curl;
use oldtailor\oauth\OAuth;

abstract class BaseGrantType implements GrantTypeInterface
{

    protected $recorder = null;

    protected $cfg = [];

    public function __construct($cfg,Recorder $recorder)
    {
        $this->cfg = $cfg;
        $this->recorder = $recorder;
    }
    
    
    public function refresh(){
        
        $curl = Curl::init();
        $curl->setHeader($key, $value);
        
        
    }
    
    public function call(){
        
        
    }
    
    public function me(){
        
        
    }
    
    
    public function token($options){
        
        $curl = Curl::init();
        
        $curl->post(OAuth::GET_ACCESS_TOKEN_URL,$options);
        
        return $curl->response;
        
    }
    
    
    
    
    
    
}