<?php
namespace oldtailor\oauth\exception;

class TokenException extends \Exception {
    
    const ERROR_TOKEN_EMPTY     = 1; //token 为空
    const ERROR_TOKEN_NOT_EXIST = 2; //不存在
    const ERROR_TOKEN_EXPIRED   = 3; //过期
    const ERROR_REFRESH_TOKEN_EMPTY = 4;
    const ERROR_REFRESH_TOKEN_EXPIRED = 5;
    
    
}