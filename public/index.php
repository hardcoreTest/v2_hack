<?php
if (function_exists("newrelic_ignore_transaction") && isset($_COOKIE['XDEBUG_SESSION'])) {
    newrelic_ignore_transaction();
}

if (!defined("PUBLIC_PATH")) {
    define("PUBLIC_PATH", realpath(__DIR__));
}

if (!defined("APP_PATH")) {
    define("APP_PATH", realpath(__DIR__ . '/../src/backend'));
}

if (!defined("TEMPLATES_PATH")) {
    define("TEMPLATES_PATH", realpath(__DIR__ . '/../src/templates'));
}

if (!defined("CONFIG_PATH")) {
    define("CONFIG_PATH", realpath(__DIR__ . '/../configs'));
}

if (!defined("RUNTIME_PATH")) {
    define("RUNTIME_PATH", realpath(__DIR__ . '/../runtime'));
}


require_once __DIR__ . '/../vendor/autoload.php';

$config = include(CONFIG_PATH . '/config.php');
$app = new Silex\Application(array('debug' => $config['debug']));


$app['settings'] = function () use ($config) {
    return $config['settings'];
};

include_once APP_PATH . '/services.php';
include_once APP_PATH . '/routers.php';
$app->run();
