<?php

namespace frontend\modules\v1\controllers;


use app\models\GiftCode;
use frontend\models\SignupForm;


use Yii;
use yii\web\UnauthorizedHttpException;
use common\models\BaseRequest;
use common\models\User;

class UserController extends BaseController
{

    /**
     * @return null
     */
    public function actionIndex()
    {
        return null;
    }

    /**
     * @return mixed|string
     * @throws UnauthorizedHttpException
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionLogin()
    {


        if (!isset($_SERVER['PHP_AUTH_PW']) && !isset($_SERVER['PHP_AUTH_USER'])) {
            throw new \yii\web\BadRequestHttpException("Missing parameter: Username or password not specified", 2000);
        }
        $password = $_SERVER['PHP_AUTH_PW'];
        $username = $_SERVER['PHP_AUTH_USER'];

        if (!$user = User::findByUsername($username)) {
            throw new UnauthorizedHttpException("Invalid credentials", 2001);
        }

        if (!Yii::$app->getSecurity()->validatePassword($password, $user->password_hash)) {
            throw new UnauthorizedHttpException("Invalid credentials", 2001);
        }

        return  User::generateAcessToken($user);


    }

    /**
     * @return string
     */
    public function actionSignup(){

        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            return [
                'success' => true,
                'message' => 'Registration successful.'
            ];
        }

    }

    /**
     * @return string
     */
    public function actionUpdate(){

        $yiiRequest = \Yii::$app->request;
        $user = User::findUserByToken($yiiRequest->getHeaders()->get("token"));

        $user->username = Yii::$app->request->post('username');
        $user->email = Yii::$app->request->post('email');

        $user->save();

        return true;
    }


    public function actionActivate($code){

        if(!$code){
            throw new UnauthorizedHttpException("No Gift Code ");
            Yii:$this->init('Hatalı gift codu');
        }


        $yiiRequest = \Yii::$app->request;
        $user = User::findUserByToken($yiiRequest->getHeaders()->get("token"));
        $gift_code = GiftCode::find()->where(['token' => $code])->one();

        if($user->status != User::STATUS_ACTIVE){

        if($gift_code){

            $user->status=User::STATUS_ACTIVE;
            if($user->save()){
                return ['status' =>true, 'message' => 'User Activated'];
            }


        }
        else {

            throw new UnauthorizedHttpException("Gift Code Invalid ");

        }
        }
        else
            {
                return ['status' =>false, 'message' => 'User active or not exists'];
        }

    }

}