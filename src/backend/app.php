<?php

if (!defined("TEMPLATE_PATH")) define("TEMPLATE_PATH", realpath(__DIR__ . "/templates"));

require_once VENDOR_PATH . '/autoload.php';

// Instantiate the app
$settings = require CONFIG_PATH . '/config.php';
$app = new Silex\Application($settings);

// Set up dependencies
require __DIR__ . '/dic.php';

// Set up routers
require __DIR__ . '/routers.php';

// Run!
return $app;