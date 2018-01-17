<?php
namespace oldtailor\oauth\exception;

class ParamException extends \Exception{
    
    private $param;
    
    public static function init($msg,$param){
        
        $self = new static($msg);
        
        $self->setParam($param);
        
        return $self;
    }
    
    public function setParam($param){
        $this->param = $param;
    }
    
    public function getParam(){
        return $this->param;
    }
    
}

