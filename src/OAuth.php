<?php
namespace oldtailor\oauth;

use Curl\Curl;

class OAuth
{

    const VERSION = "2.0";

    const GET_AUTH_CODE_URL = "http://app.midianvip.cn/authorize";

    const GET_ACCESS_TOKEN_URL = "http://app.midianvip.cn/oauth/token";

    const GET_OPENID_URL = "http://app.midianvip.cn/userinfo";

    public $app_id;

    public $app_key;

    public $redirect_uri;

    protected $recorder;

    protected $error;

    function __construct()
    {
        $this->recorder = new Recorder();
    }

    public function curl()
    {
        $curl = new Curl();
        
        $curl->setHeader('Authorization', 'Bearer ' . $this->recorder->read('token'));
        
        return $curl;
    }

    // 用户登录
    public function login()
    {
        $state = md5(uniqid(rand(), TRUE));
        
        $this->recorder->write('state', $state);
        
        $keysArr = array(
            "response_type" => "code",
            "client_id" => $this->app_id,
            "redirect_uri" => $this->redirect_uri,
            "state" => $state,
            "scope" => 'openid'
        );
        
        $login_url = Utils::combineURL(self::GET_AUTH_CODE_URL, $keysArr);
        
        header("Location:$login_url");
    }

    // 登录回调
    public function callback()
    {
        $code = Input::get('code') or exit();
        // 检测令牌，防止工机
        $this->recorder->read('state') != Input::get('state') && exit();
        
        // 令牌请求参数
        $params = array(
            "grant_type" => "authorization_code",
            "client_id" => $this->app_id,
            "redirect_uri" => urlencode($this->redirect_uri),
            "client_secret" => $this->app_key,
            "code" => $code
        );
        
        $response = $this->curl()->post(self::GET_ACCESS_TOKEN_URL, $params);
        
        $this->recorder->write('token', $response->access_token);
        
        return $this->recorder->read('token');
    }

    public function me()
    {
        
        $res = $this->curl()->get(self::GET_OPENID_URL);
        
        echo $res;
    }
}