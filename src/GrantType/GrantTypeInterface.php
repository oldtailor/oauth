<?php
namespace oldtailor\oauth\GrantType;

interface GrantTypeInterface{
    
    /**
     * 获取token
     */
    public function refresh();
    
    public function token($options);
    
    public function call();
    
    public function me();
    
    
    
    
    
    
}