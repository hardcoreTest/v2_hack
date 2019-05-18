<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 06.06.2016
 * Time: 17:37
 */

namespace schedule\Controller;


use schedule\Model\Schedule;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;


class ScheduleController
{
    private $app;

    const DIRECTORY = "schedule";

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function index(Application $app, Request $request)
    {
        /** @var Schedule $schedule */
        $schedule = $app['model.schedule'];
        $schedules = $schedule->search([
            Schedule::FIELD_DATE =>
                [
                    [
                        'type' => '>=',
                        'value' => date("Y-m-d"),
                    ]
                ],
        ], [Schedule::FIELD_DATE => 'asc', Schedule::FIELD_LESSON => 'asc']

        );
        return $app['twig']->render(
            self::DIRECTORY . '/index.twig',
            array(
                'schedules' => $schedules,
            )
        );
    }

    public function about(Application $app, Request $request){
        return $app['twig']->render(self::DIRECTORY . '/about.twig');
    }

    public function brochure(Application $app, Request $request){
        return $app['twig']->render(self::DIRECTORY . '/brochure.twig');
    }
    public function poster(Application $app, Request $request){
        return $app['twig']->render(self::DIRECTORY . '/poster.twig');
    }
    public function prezi(Application $app, Request $request){
        return $app['twig']->render(self::DIRECTORY . '/prezi.twig');
    }
    public function price(Application $app, Request $request){
        return $app['twig']->render(self::DIRECTORY . '/price.twig');
    }
    public function video(Application $app, Request $request){
        return $app['twig']->render(self::DIRECTORY . '/video.twig');
    }
    public function work(Application $app, Request $request){
        return $app['twig']->render(self::DIRECTORY . '/work.twig');
    }

}