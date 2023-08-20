<?php

$GLOBALS['config'] = array(
    'mysql' => array(
        'host' => '127.0.0.1',
        'username' => 'root',
        'password' => '',
        'db' => 'hospital'
    ),
    'remember' =>array(
        'cookie_name' => 'hash',
        'cookie_expiry' => '3600'
    ),
    'session' =>array (
        'session_name' =>'user',
        'token_name' => 'token',
        'session_table' => 'tableName'
    )
);

spl_autoload_register(function($class){
    require_once 'php/classes/'.$class.'.php';
});

require_once 'php/functions/sanitize.php';
require_once 'php/classes/OverideData.php';

