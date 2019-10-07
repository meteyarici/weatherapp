<?php

namespace frontend\modules\v1\models;

use common\models\ApiUser;
use common\models\orm\api\ApiUsers;
use common\models\orm\api\ApiUsersToken;
use Yii;
use yii\filters\RateLimitInterface;
use yii\web\IdentityInterface;
use yii\web\TooManyRequestsHttpException;
use yii\web\UnauthorizedHttpException;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ApiUsers implements IdentityInterface, RateLimitInterface
{
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::find()
            ->where(['id' => $id])
            ->andWhere(["in", "status", [self::STATUS_ACTIVE, self::STATUS_SOFT_CLOSED]])
            ->one();
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        Yii::$app->db->enableSlaves = false;

        // Only token
        if (!empty($_SERVER['PHP_AUTH_PW']) && !empty($_SERVER['PHP_AUTH_USER'])) {
            throw new UnauthorizedHttpException("Invalid credentials", Yii::$app->params['invalidCredentials']);
        }

        if ($token) {
            $usersToken = ApiUsersToken::find()
                ->joinWith("user u")
                ->where(['token' => $token])
                ->andWhere(["in", "status", [self::STATUS_ACTIVE, self::STATUS_SOFT_CLOSED]])
                ->andWhere(['>', ApiUsersToken::tableName() . '.expires_at', time()])
                ->one();

            if (!$usersToken) {
                throw new UnauthorizedHttpException("Token expired", Yii::$app->params['tokenExpired']);
            }

            Yii::$app->session->set("isTestUser", $usersToken->user->is_test_user);
            Yii::$app->session->set("partner", $usersToken->user->partner_id);
            Yii::$app->session->set("appCheck", $usersToken->user->app_id_check);
            Yii::$app->session->set("userType", $usersToken->user->type);
            Yii::$app->session->set("productCardName", $usersToken->user->product_card_name);

            ApiUser::checkIp($usersToken->user);
            ApiUser::checkAppId($usersToken->user);

            return $usersToken->user;

        }

        return null;
    }


    /**
     * @param $username
     * @return array|\yii\db\ActiveRecord|null
     */
    public static function findByUsername($username)
    {
        return static::find()
            ->where(['username' => $username])
            ->andWhere(["in", "status", [self::STATUS_ACTIVE, self::STATUS_SOFT_CLOSED]])
            ->one();
    }

    /**
     * @param $token
     * @return array|\yii\db\ActiveRecord|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::find()
            ->where(['password_reset_token' => $token])
            ->andWhere(["in", "status", [self::STATUS_ACTIVE, self::STATUS_SOFT_CLOSED]])
            ->one();
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * @param \yii\web\Request $request
     * @param \yii\base\Action $action
     * @return array
     */
    public function getRateLimit($request, $action)
    {
        return [$this->rate_limit, $this->rate_limit_period]; // $rateLimit requests per time
    }

    /**
     * @param \yii\web\Request $request
     * @param \yii\base\Action $action
     * @return array
     */
    public function loadAllowance($request, $action)
    {
        return [$this->allowance, $this->allowance_updated_at];
    }

    /**
     * @param \yii\web\Request $request
     * @param \yii\base\Action $action
     * @param int $allowance
     * @param int $timestamp
     */
    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
        $this->allowance = $allowance;
        $this->allowance_updated_at = $timestamp;
        $this->save();
    }

    public function checkAuthRateLimit()
    {
        if (!$this->auth_check) {
            return null;
        }

        $current = time();

        $limit = Yii::$app->params["authRateLimit"];
        $window = Yii::$app->params["authRateLimitPeriod"];
        list ($allowance, $timestamp) = [$this->auth_allowance, $this->auth_allowance_updated_at];

        $allowance += (int)(($current - $timestamp) * $limit / $window);

        if ($allowance > $limit) {
            $allowance = $limit;
        }

        if ($allowance < 1) {
            $this->auth_allowance = 0;
            $this->auth_allowance_updated_at = $current;
            $this->save();
            throw new TooManyRequestsHttpException("Too many auth-token requests");
        } else {
            $this->auth_allowance = $allowance - 1;
            $this->auth_allowance_updated_at = $current;
            $this->save();
        }
    }
}