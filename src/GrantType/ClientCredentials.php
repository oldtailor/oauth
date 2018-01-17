<?php
namespace oldtailor\oauth\GrantType;

class ClientCredentials extends BaseGrantType{
    
    
    public function token($options)
    { 
        parent::token($this->cfg);
    }
    
    
    
}
