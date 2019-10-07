<?php
namespace frontend\modules\v1;

use Yii;

class Module extends \yii\base\Module
{
    public function init()
    {
        parent::init();

        Yii::$app->user->enableSession = false;

        Yii::$app->set('user', [
            'class' => 'yii\web\User',
            'identityClass' => 'frontend\modules\v1\models\User',
            'enableAutoLogin' => false,
            'idParam' => 'id'
        ]);
    }
}