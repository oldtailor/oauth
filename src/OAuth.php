<?php
namespace oldtailor\oauth;

use Curl\Curl;

class OAuth
{

    const VERSION = "2.0";

    const GET_AUTH_CODE_URL = "http://app.midianvip.cn/authorize";

    const GET_ACCESS_TOKEN_URL = "http://app.midianvip.cn/oauth/token";

    const GET_OPENID_URL = "http://app.midianvip.cn/userinfo";
    
    const API_URL = "http://app.midianvip.cn/api";

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

    // 鐢ㄦ埛鐧诲綍
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

    // 鐧诲綍鍥炶皟
    public function callback()
    {
        $code = Input::get('code') or exit('code');
        // 妫�娴嬩护鐗岋紝闃叉宸ユ満
        $this->recorder->read('state') != Input::get('state') && exit('state');
        
        // 浠ょ墝璇锋眰鍙傛暟
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
        $curl = $this->curl();
        
        $res = $curl->get(self::GET_OPENID_URL);
        
        if($curl->error) return null;
        
        return $res;
    }
    
    
    /**
     * api 请求
     * @param string $method
     * @param array $params
     */
    public function api($method,$params=[]){
        
        $curl = $this->curl();
        $curl->post(self::API_URL.'/'.$method,$params);
        
        return $curl->response;
    }
    
    
}