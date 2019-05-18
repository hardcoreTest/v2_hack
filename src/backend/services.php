<?php
/**
 * @var Silex\Application $app
 */
$app;


$app['model.user'] = $app->factory(function () use ($app) {
    return new \schedule\Model\Users($app);
});


$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => 'pdo_mysql',
        'host' => $app['settings']['db']['host'],
        'dbname' => $app['settings']['db']['dbname'],
        'user' => $app['settings']['db']['username'],
        'password' => $app['settings']['db']['password'],
        'charset' => 'utf8mb4',
    ),
));

$app['GUID.generate'] = $app->factory(function () {
    $data = openssl_random_pseudo_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0010
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 01
    return strtoupper(vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4)));
});

$app->register(new Silex\Provider\SessionServiceProvider());

$app['session.storage.handler'] = function () use ($app) {
    $dns = "mysql:host={$app['settings']['db']['host']};dbname={$app['settings']['db']['dbname']};charset=utf8mb4";
    $pdo = new PDO($dns, $app['settings']['db']['username'], $app['settings']['db']['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return new \Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler(
        $pdo,
        $app['settings']['session'],
        $app['session.storage.options']
    );
};

$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new Silex\Provider\LocaleServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallbacks' => array('en'),
));
$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\CsrfServiceProvider());

$app->register(new Silex\Provider\ServiceControllerServiceProvider());

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => TEMPLATES_PATH,
    'twig.options' => array(
        'cache' => RUNTIME_PATH . '/twig-cache',
        'debug' => $app['debug'],
        'auto_reload' => $app['debug'],
        'strict_variables' => $app['debug'],

    )
));

$app->extend('twig', function ($twig, $app) {
    /** @var \Twig_Environment $twig */
    $twig->addGlobal('App', $app);
    $function = new \Twig_SimpleFunction('dump_session_id', function () {
        return session_id();
    });
    $twig->addFunction($function);
    return $twig;
});


