<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 01.08.2016
 * Time: 10:33
 */

namespace schedule\Controller\Provider;


use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class DefaultProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->get('', function (Application $app, Request $request) {
            return  $app->redirect($app['url_generator']->generate('map_index'), 302);
        })->bind("default");

        return $controllers;
    }
}