<?php

namespace frontend\modules\v1\controllers;


class WeatherController extends BaseController
{

    public function actionGet()
    {
        date_default_timezone_set('Europe/Istanbul');
        return date('T');
        return 'your weather 28 cn';
    }

}