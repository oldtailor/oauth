<?php
namespace oldtailor\oauth;

use Curl\Curl;
use think\Cache;
use oldtailor\sdk\errors\ConnectError;
use oldtailor\sdk\errors\SystemError;
use oldtailor\sdk\errors\UserAuthError;
use oldtailor\sdk\errors\ParamError;
use oldtailor\sdk\errors\LogicError;

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
    
    public $app_id;

    public $app_key;

    public $redirect_uri;

    function __construct()
    {
        $this->recorder = new Recorder();
    }

    public function curl()
    {
        $curl = new Curl();
        
        return $curl;
    }

    // 登录
    public function login()
    {
        $state = md5(uniqid(rand(), TRUE));
        
        $this->recorder->write('state', $state);
        
        $keysArr = array(
            "response_type" => "code",
            "client_id" => $this->app_id,
            "redirect_uri" => $this->redirect_uri,
            "state" => $state,
            "scope" => 'openid member'
        );
        
        $login_url = Utils::combineURL(self::GET_AUTH_CODE_URL, $keysArr);
        
        header("Location:$login_url");
    }

    // 登录回调
    public function callback()
    {
        $code = Input::get('code') or exit('code');
        
        // 验证 state
        $this->recorder->read('state') != Input::get('state') && exit('state');
        
        // 获取 token
        $params = array(
            "grant_type" => self::GRANT_TYPE_AUTHORIZATION_CODE,
            "client_id" => $this->app_id,
            "redirect_uri" => $this->redirect_uri,
            "client_secret" => $this->app_key,
            "code" => $code
        );
        
        $curl = $this->curl();
        
        $response = $curl->post(self::GET_ACCESS_TOKEN_URL, $params);
        
        $this->recorder->write('token', $response->access_token);
        
        return $this->recorder->read('token');
    }
    
    
    public function me()
    {
        $curl = $this->curl();
        
        $curl->setHeader('Authorization', 'Bearer ' . $this->recorder->read('token') );
        
        $curl->get(self::GET_OPENID_URL);

        if ($curl->error) return null;

        $this->recorder->write('openid',$curl->response->user_id);

        return json_decode($curl->rawResponse, true);
    }

    public function getOpenId(){

        return $this->recorder->read('openid');
    }

    /**
     * api 请求
     * 
     * @param string $method
     * @param array $params
     */
    public function api($method, $token ,$params = [])
    {
        $curl = $this->curl();
        
        $curl->setHeader('Authorization', 'Bearer ' . $token );
        
        $curl->post(self::API_URL . '/' . $method, $params);

       // print_r([$token,$curl->rawResponse]);

        if($curl->error) throw new ConnectError("api 网络错误");
        
        $resp = $curl->response;
        
        if(!is_object($resp) ) throw new SystemError($resp);

        //print_r($resp);

        if($resp->res_code != "SUCCESS" ){
            
            switch ($resp->err_code){
                case 1:
                    throw new SystemError($resp->err_code_des);
                case 4:
                    throw new UserAuthError($resp->err_code_des);
                case 5:
                    throw new ParamError($resp->err_code_des);
                case 6:
                    throw new LogicError($resp->err_code_des,$resp->err_code_sub);
                default:
                    throw new \Exception("unknown");
            }
        }
        
        return $resp->response;
    }
    
    public function member($method,$params=[]){
        
        return $this->api('member.'.$method, $this->recorder->read('token'),$params);
    }

    /**
     * @param $method
     * @param array $params
     * @return mixed
     */
    public function common($method,$params=[]){
        
        $token = Cache::get('client_token');
        
        $token || $token = $this->getToken();
        
        return $this->api('common.'.$method , $token , $params);
    }
    
    /**
     * 获取 client_credentials的token
     */
    public function getToken()
    {
        $params = [
            'grant_type' => self::GRANT_TYPE_CLIENT_CREDENTIALS,
            'client_id' => $this->app_id,
            'client_secret' => $this->app_key
        ];
        
        $curl = $this->curl();
        
        $resp = $curl->post(self::GET_ACCESS_TOKEN_URL, $params);

        if ($curl->error) return null;
        
        Cache::set('client_token', $resp->access_token , $resp->expires_in);
        
        return $resp->access_token;
    }
    
    
}