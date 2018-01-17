<?php
namespace oldtailor\oauth;

/**
 * api调用请求返回
 * @author metooweb
 * @property string  $res_code
 * @property string  $response
 * @property integer $err_code
 * @property string  $err_code_sub
 * @property string  $err_code_des
 */
class Api {
    
    const FAIL    = 'FAIL';
    const SUCCESS = 'SUCCESS';
    
    const ERROR_SYSTEM          = 1; //系统错误
    const ERROR_TOKEN           = 2; //token错误
    const ERROR_UNKNOWN_METHOD  = 4; //调用未知方法
    const ERROR_PARAMS          = 5; //参数错误
    const ERROR_LOGICS          = 6; //逻辑错误
    const ERROR_NETWORK         = 8; //网络错误
    
    const ERROR_TOKEN_INVALID   = 100; //token无效
    const ERROR_TOKEN_SCOPE     = 101; //token权限不足
    const ERROR_TOKEN_EXPIRED   = 102; //token过期
    
    const USER_TYPE_UNKNOWN     = 1;
    const USER_TYPE_ADMIN       = 2;
    const USER_TYPE_MEMBER      = 4;
    const USER_TYPE_STORE       = 8;
    
    private $data = [];
    
    public function __construct($data){
        $this->data = $data;
    }
    
    public static function init($data){
        return new static($data);
    }
    
    public function __get($name){
        return empty($this->data[$name]) ? null : $this->data[$name];
    }
    
    public function __set($name , $value){
        
        $this->data[$name] = $value;
    }
    
}