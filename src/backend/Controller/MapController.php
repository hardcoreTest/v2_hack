<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 06.06.2016
 * Time: 17:37
 */

namespace schedule\Controller;


use schedule\Model\Users;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;


class MapController
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

    public function map(Application $app, Request $request)
    {
        /** @var Users $model */
        $model = $app['model.user'];
        $users = $model->search(
            [Users::FIELD_TYPE_USER => ['collector', 'utilizer']],
            [Users::FIELD_TYPE_WASTE=>'asc'],
            0);
        $groups = $model->getGroupByType($users);
        return $app['twig']->render(
            self::DIRECTORY . '/map.twig',
            [
                "groups" => $groups,
            ]
        );
    }


}