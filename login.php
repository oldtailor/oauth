<?php

use oldtailor\oauth\OAuth;

include 'vendor/autoload.php';

include 'src/OAuth.php';

$oauth = new OAuth();

$oauth->app_id = "test";
$oauth->app_key = "test";
$oauth->redirect_uri = 'http://127.0.0.1/login.php';

echo $oauth->callback();