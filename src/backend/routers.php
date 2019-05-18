<?php
/** @var Silex\Application $app */

use \Symfony\Component\HttpFoundation\Request;

$app->get('/hello/{name}', function(Request $request, $name) use ($app) {
    return $app['twig']->render('hello.twig', array(
        'name' => $name,
    ));
})->bind("test_test");




$app->mount("/", new schedule\Controller\Provider\DefaultProvider());
$app->mount("/", new schedule\Controller\Provider\ScheduleControllerProvider());



