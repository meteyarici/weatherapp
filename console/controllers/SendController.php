<?php

namespace console\controllers;

use common\models\User;
use yii\console\Controller;

class SendController extends Controller
{
    /**
     * @param $minutes
     * @param $total
     * @return string
     */

    public function init()
    {

        date_default_timezone_set('UTC');

        $hour = date('H');
        $diff = $hour - 9;

        if ($diff == 0) {
            $users = User::find()->where(['timezone_offset' => '0'])->andWhere(['status' => User::STATUS_ACTIVE])->all();
        } else {
            if ($diff < 0) {
                $tzdiff = abs($diff);
                $tzdiff = str_pad($tzdiff, 2, "0", STR_PAD_LEFT);
                $tzdiff = '+' . $tzdiff;

            } else {
                $tzdiff = $diff * -1;
                $tzdiff = ltrim($tzdiff, '-');
                $tzdiff = str_pad($tzdiff, 2, "0", STR_PAD_LEFT);
                $tzdiff = '-' . $tzdiff;

            }
            $users = User::find()->where(['timezone_offset' => $tzdiff])->andWhere(['status' => User::STATUS_ACTIVE])->all();
        }

        foreach ($users as $user) {
            echo $user->mail . " - adresine mail gönderildi \n";
        }

        exit;


    }


}