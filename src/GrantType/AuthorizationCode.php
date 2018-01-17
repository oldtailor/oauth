<?php
namespace oldtailor\oauth\GrantType;

use oldtailor\oauth\Curl;
use oldtailor\oauth\OAuth;

class AuthorizationCode extends BaseGrantType {
    
    
    /**
     * 获取token
     * @param array $options:
     * @param string $options.code
     */
    public function token($options)
    {
     
        $options = array_merge($this->cfg,$options);
        
        parent::token($options);
        
    }

    
    
    
}