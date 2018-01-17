<?php
namespace oldtailor\oauth;

use Curl\Curl;

class OAuth
{

    protected $recorder;

    protected $error;

    const VERSION = "2.0";

    const GET_AUTH_CODE_URL = "http://app.midianvip.cn/authorize";

    const GET_ACCESS_TOKEN_URL = "http://app.midianvip.cn/oauth/token";

    const GET_OPENID_URL = "http://app.midianvip.cn/userinfo";

    const API_URL = "http://app.midianvip.cn/api";
    
    const GRANT_TYPE_USER_CREDENTIALS = 'password';
    
    const GRANT_TYPE_AUTHORIZATION_CODE = 'authorization_code';
    
    const GRANT_TYPE_CLIENT_CREDENTIALS = 'client_credentials';
    
    private $cfg;
    
    public $app_id;

    public $app_key;

    public $redirect_uri;

    function __construct($grant_type)
    {
        
        $this->cfg = Config::get($grant_type); //获取到不同认证类型的配置
        
        $this->recorder = new Recorder();
        
    }
    
    
    
    
}