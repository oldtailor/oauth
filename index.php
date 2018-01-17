<?php

use oldtailor\oauth\Config;
use oldtailor\oauth\OAuth;
use oldtailor\oauth\options\TokenGet;
use oldtailor\oauth\Api;

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

include 'vendor/autoload.php';

$cfg = Config::init();

$cfg->client_id = 'mgr';
$cfg->client_secret = 'test';
$cfg->grant_type = OAuth::GRANT_TYPE_USER_CREDENTIALS;
$cfg->scope = 'openid';

Config::set('public', $cfg);


$cfg = Config::init();

$cfg->client_id = 'mgr';
$cfg->client_secret = 'test';
$cfg->grant_type = OAuth::GRANT_TYPE_CLIENT_CREDENTIALS;
$cfg->scope = 'openid common';

Config::set('client', $cfg);


print_r($_SESSION);
$oauth = new OAuth('client');

$oauth->token(TokenGet::init([
    
//     'usertype' => Api::USER_TYPE_ADMIN,
//     'username' => 'admin',
//     'password' => 'hmviplcf!@#',
    
]));


$me = $oauth->call('common.store.get', ['id'=>428]);

print_r($me);