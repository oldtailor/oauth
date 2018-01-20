<?php
namespace oldtailor\oauth;

use oldtailor\oauth\options\TokenGet;
use oldtailor\oauth\exception\ApiException;
use oldtailor\oauth\exception\AccountException;
use oldtailor\oauth\exception\TokenException;
use oldtailor\oauth\exception\SystemException;
use oldtailor\oauth\exception\ParamException;
use oldtailor\oauth\exception\LogicException;

class OAuth
{

    const URL_ME = "http://app.midianvip.cn/userinfo";

    const URL_API = "http://app.midianvip.cn/api/";

    const URL_CODE = "http://app.midianvip.cn/authorize";

    const URL_TOKEN = "http://app.midianvip.cn/oauth/token";

    const GRANT_TYPE_USER_CREDENTIALS = 'password';

    const GRANT_TYPE_AUTHORIZATION_CODE = 'authorization_code';

    const GRANT_TYPE_CLIENT_CREDENTIALS = 'client_credentials';

    const GRANT_TYPE_REFRESH_TOKEN = 'refresh_token';

    private $recorder;

    /**
     * 配置
     *
     * @var Config
     */
    private $cfg;
    private static $pool = [];

    function __construct($cfg)
    {
        $this->cfg = is_string($cfg) ? Config::get($cfg) : $cfg;
        
        $this->recorder = new Recorder($this->cfg->recorder_key , $this->cfg->grant_type == static::GRANT_TYPE_CLIENT_CREDENTIALS );
    }

    /**
     * @param string|Config $cfg
     * @return static
     */
    public static function init($cfg){
        
        $cfg = is_string($cfg) ? Config::get($cfg) : $cfg;
        
        if( !isset( static::$pool[$cfg->name] ) ) {
            static::$pool[$cfg->name] = new static($cfg);
        }
        
        return static::$pool[$cfg->name];
    }
    
    public function login($user_type = Api::USER_TYPE_MEMBER)
    {
        
        $state = md5(uniqid(rand(), TRUE));
        
        $this->recorder->write('state', $state);
        
        $keysArr = array(
            "response_type" => "code",
            "client_id" => $this->cfg->client_id,
            "redirect_uri" => $this->cfg->redirect_uri,
            "state" => $state,
            "scope" => $this->cfg->scope
        );
        
        $login_url = Utils::combineURL(self::URL_CODE, $keysArr);
        
        header("Location:$login_url"); 
    }
    
    public function callback()
    {
        $code = Input::get('code') or exit('code');
        // 验证 state
        $this->recorder->read('state') != Input::get('state') && exit('state');
        
        $data = ['redirect_uri'=>$this->cfg->redirect_uri,'code'=>$code];
        
        return $this->token(TokenGet::init($data));
    }
    
    public function token(TokenGet $options)
    {
        $data = [
            'grant_type' => $this->cfg->grant_type,
            'client_id' => $this->cfg->client_id,
            'client_secret' => $this->cfg->client_secret,
            'scope'         => $this->cfg->scope,
        ];
        
        switch ($this->cfg->grant_type) {
            case self::GRANT_TYPE_USER_CREDENTIALS:
                $data = array_merge($data, [
                    'usertype' => $options->usertype,
                    'username' => $options->username,
                    'password' => $options->password
                ]);
                break;
            case self::GRANT_TYPE_AUTHORIZATION_CODE:
                $data = array_merge($data, [
                    'code' => $options->code,
                    'redirect_uri' => $this->cfg->redirect_uri
                ]);
                break;
            case self::GRANT_TYPE_CLIENT_CREDENTIALS:
                break;
        }
        
        $now = time();
        
        $curl = Curl::init();
        $curl->post(self::URL_TOKEN, $data);
        
        if (! is_object($curl->response))
            throw new ApiException($curl->response);
        
        if ($curl->error)
            throw ($curl->errorCode == 401 ? new AccountException("用户名或密码错误") : new ApiException($curl->response->error . ':' . $curl->response->error_description));
        
            print_r($curl->response);
            
        $this->recorder->write('token', $curl->response->access_token);
        empty($curl->response->refresh_token) || $this->recorder->write('refresh_token', $curl->response->refresh_token);
        $this->recorder->write('time_expired', $now + $curl->response->expires_in);
        
        return true;
    }

    public function refresh()
    {
        if($this->cfg->grant_type == self::GRANT_TYPE_CLIENT_CREDENTIALS ){
            
            return $this->token(TokenGet::init());
        }
        
        $refresh_token = $this->recorder->read('refresh_token');
        if (! $refresh_token)
            throw new TokenException('refresh_token empty', TokenException::ERROR_REFRESH_TOKEN_EMPTY);
        
        $now = time();
        $curl = Curl::init();
        $curl->post(self::URL_TOKEN, [
            'grant_type' => self::GRANT_TYPE_REFRESH_TOKEN,
            'client_id' => $this->cfg->client_id,
            'client_secret' => $this->cfg->client_secret,
            'refresh_token' => $refresh_token
        ]);
        
        if ($curl->error) {
            throw new TokenException();
        }
        
        $this->recorder->write('token', $curl->response->access_token);
        empty($curl->response->refresh_token) || $this->recorder->write('refresh_token', $curl->response->refresh_token);
        $this->recorder->write('time_expired', $now + $curl->response->expires_in);
    }

    public function call($method, $params)
    {
        $token = $this->recorder->read('token');
        
        if (!$token) {
            
            if( $this->cfg->grant_type == static::GRANT_TYPE_CLIENT_CREDENTIALS ){    
                $this->refresh();
                return $this->call($method, $params);
            }else{
                throw new TokenException('token empty', TokenException::ERROR_TOKEN_EMPTY);
            }
            
        }
        
        if ($this->recorder->read('time_expired') < time()) { // token 过期，刷新token
            $this->refresh();
            return $this->call($method, $params);
        }
        
        $curl = Curl::init();
        $curl->setHeader('Authorization', 'Bearer ' . $token);
        $curl->post(self::URL_API.$method,$params);
        
        if($curl->error){
            throw new ApiException();
        }
        
        $res = Api::init( (array) $curl->response );
        
        if($res->res_code == Api::SUCCESS) return $res->response;
        
        switch ($res->err_code){
            case Api::ERROR_SYSTEM: throw new SystemException($res->err_code_des);
            case Api::ERROR_PARAMS: throw ParamException::init($res->err_code_des, '');
            case Api::ERROR_LOGICS: throw new LogicException($res->err_code_des, $res->err_code_sub);
            default:throw new ApiException($res->err_code_des);
        }
        
    }

    public function me()
    {
        if (! $token = $this->recorder->read('token')) {
            //throw new TokenException('token empty', TokenException::ERROR_TOKEN_EMPTY);
            return null;
        }
        
        if ($this->recorder->read('time_expired') < time()) { // token 过期，刷新token
            $this->refresh();
            return $this->me();
        }
        
        $curl = Curl::init();
        $curl->setHeader('Authorization', 'Bearer ' . $token);
        $curl->get(self::URL_ME);
        
        return $curl->response;
    }
}
