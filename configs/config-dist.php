<?php

$config = array (
    'settings' => array (
        'remember_me_key' => 'ac658779-d808-4925-b891-e48da007a111',
        'debug' => false,
        'gearman' => array(
            'host' => '127.0.0.1',
        ),
        'db' => array(
            'host' => '127.0.0.1',
            'username' => 'root',
            'password' => '',
            'dbname' => 'forum',
        ),
        'session' => array(
            'db_table'        => 'session',
            'db_id_col'       => 'session_id',
            'db_data_col'     => 'session_value',
            'db_lifetime_col' => 'session_lifetime',
            'db_time_col'     => 'session_time',
        ),
        // View settings
        'view' => [
            'template_path' => APP_PATH . '/templates',
            'twig' => [
                'cache' => RUNTIME_PATH . '/twig',
                'debug' => true,
                'auto_reload' => true,
            ],
        ],

        // monolog settings
        'logger' => [
            'name' => 'app',
            'path' => RUNTIME_PATH . '/app.log',
        ],

        'elastic' => array(
            'hosts' => array(
                'http://127.0.0.1:9200',
            ),
        ),
        'baseUrl' => '/',
        'displayErrorDetails' => false,

    ),
    'debug' => false,
    'displayErrorDetails' => false,


);
if (getenv('APPLICATION_ENV') && getenv('APPLICATION_ENV') == 'development') {
    $config['debug'] = true;
    $config['displayErrorDetails'] = true;
    $config['settings']['displayErrorDetails'] = true;
    opcache_reset();
}
return $config;