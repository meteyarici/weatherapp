<?php
namespace frontend\tests\unit\models;

use frontend\models\SignupForm;

class UserSignupTest extends \Codeception\Test\Unit
{
    public function testUserSignup()
    {

        $model = new SignupForm([
            'username' => 'some_username',
            'email' => 'some_email@example.com',
            'password' => 'some_password',
        ]);
        $user = $model->signup();
        expect($user)->true();
        /** @var \common\models\User $user */
        $user = $this->tester->grabRecord('common\models\User', [
            'username' => 'some_username',
            'email' => 'some_email@example.com',
            'status' => \common\models\User::STATUS_INACTIVE
        ]);
    }

    public function testNotCorrectSignup()
    {
        $model = new SignupForm([
            'username' => 'troy.becker',
            'email' => 'nicolas.dianna@hotmail.com',
            'password' => 'some_password',
        ]);
        expect_not($model->signup());
        expect_that($model->getErrors('username'));
        expect_that($model->getErrors('email'));
        expect($model->getFirstError('username'))
            ->equals('This username has already been taken.');
        expect($model->getFirstError('email'))
            ->equals('This email address has already been taken.');
    }
    
}
