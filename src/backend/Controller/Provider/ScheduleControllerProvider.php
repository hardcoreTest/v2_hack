<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 01.08.2016
 * Time: 16:05
 */

namespace schedule\Controller\Provider;


use schedule\Controller\ScheduleController;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
class ScheduleControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        // creates a new controller based on the default route
        $controllers = $app['controllers_factory'];
        $app['controllers.bundle.schedule'] = function() use ($app) {
            return new ScheduleController($app);
        };

        $controllers->get('about', "controllers.bundle.schedule:about")->bind("schedule_about");
        $controllers->get('brochure', "controllers.bundle.schedule:brochure")->bind("schedule_brochure");
        $controllers->get('poster', "controllers.bundle.schedule:poster")->bind("schedule_poster");
        $controllers->get('prezi', "controllers.bundle.schedule:prezi")->bind("schedule_prezi");
        $controllers->get('price', "controllers.bundle.schedule:price")->bind("schedule_price");
        $controllers->get('video', "controllers.bundle.schedule:video")->bind("schedule_video");
        $controllers->get('work', "controllers.bundle.schedule:work")->bind("schedule_work");
        return $controllers;
    }
}