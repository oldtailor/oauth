<?php
namespace oldtailor\oauth\GrantType;

use oldtailor\oauth\OAuth;
use oldtailor\oauth\Curl;

class UserCredentials extends BaseGrantType{

    /**
     * 获取token
     * @param array $options:
     * @property integer $usertype
     * @property string  $username
     * @property string  $password
     */
    public function token($options)
    {
            
        $options = array_merge($this->cfg,$options);
        
        parent::token($options);
        
        
    }
    
}