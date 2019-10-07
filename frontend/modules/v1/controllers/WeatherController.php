<?php

namespace frontend\modules\v1\controllers;


use common\models\User;

class WeatherController extends BaseController
{

    /**
     * @return false|string
     */
    public function actionGet()
    {
       if(User::isActive( \Yii::$app->request->getHeaders()->get("token"))){

           date_default_timezone_set('Europe/Istanbul');
           return date('T');
           return 'your weather 28 cn';
       }
    }

}