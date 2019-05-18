<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 01.08.2016
 * Time: 16:05
 */

namespace schedule\Controller\Provider;


use schedule\Controller\MapController;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
class MapControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        // creates a new controller based on the default route
        $controllers = $app['controllers_factory'];
        $app['controllers.bundle.map'] = function() use ($app) {
            return new MapController($app);
        };

        $controllers->get('', "controllers.bundle.map:map")->bind("map_index");
        $controllers->post('/save', "controllers.bundle.map:save")->bind("map_save");
        return $controllers;
    }
}