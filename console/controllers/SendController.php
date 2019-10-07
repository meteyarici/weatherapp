<?php

namespace console\controllers;

use DateTime;
use common\models\User;
use yii\console\Controller;

class SendController extends Controller
{
    /**
     * @param $minutes
     * @param $total
     * @return string
     */

    public function init() {



        date_default_timezone_set('Europe/Istanbul');
        //date_default_timezone_set('Asia/Dhaka');
       // date_default_timezone_set('UTC');


        $dateTimeZone = new \DateTimeZone("UTC");

        $date = new DateTime(null, $dateTimeZone);

        //echo $dateTimeZone->getOffset($date)/60/60;

        $hour = date('H');
        echo  $hour - 9  ;


        /*
        $timezone = date('T');
        echo $timezone;
        $hour = date('H');
        echo " - " . $hour;
        */
        //if hour = 21;
        //UTC ye gönder


        //else
        //echo 20 + 2 ;

       // echo date('H') . "\n";
       // exit;

        //eğer saat 8 se +01 lere gönder

        //echo $hour - 21 . "\n";

        for ($i=0; $i != 24; $i++)
         {
             //echo $i - 9;
          }


          exit;
        $users = User::find()
            ->where(['status' => User::STATUS_ACTIVE])
            ->all();

        foreach ($users as $user) {

            echo date('Y-m-d H:i:s T', time()) . ' - '.  $user->username . " kullanıcısına havadurumu gönderildi \n";

        }

        exit;

    }



}