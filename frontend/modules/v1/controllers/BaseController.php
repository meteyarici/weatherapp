<?php


namespace frontend\modules\v1\controllers;

use Yii;
use common\models\App;
use common\models\AppIps;
use common\models\User;
use common\models\UserAllowedApp;
use yii\web\Controller;
use yii\web\BadRequestHttpException;

class BaseController extends Controller
{

    public $allowedUrls = [
        "v1/user/login",
        "v1/user/signup",
        ];

    /**
     * @param $action
     * @return bool
     * @throws BadRequestHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function beforeAction($action)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (!in_array(rtrim(Yii::$app->request->getPathInfo(),'/'), $this->allowedUrls) ) {

          if($this->getUserFromToken()){
              return true;
          }
          else {
              throw new BadRequestHttpException("User access token is missing");
              return false;
          }

    }

        return true;

    }

    protected function findToken(){

        return false;

    }

    /**
     * @return User
     * @throws BadRequestHttpException
     * @throws \Exception
     */
    protected function getUserFromToken()
    {

        $yiiRequest = \Yii::$app->request;
        $accessToken = $yiiRequest->getHeaders()->get("token");

        if (empty($accessToken)) {
            throw new BadRequestHttpException("User access token is missing");
        }

        $token = User::findToken($accessToken);
        if (!$token) {
            throw new BadRequestHttpException("User not found");
        }

       return true;
    }
}