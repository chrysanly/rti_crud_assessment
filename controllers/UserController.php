<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use app\models\User;


class UserController extends Controller
{
    public $enableCsrfValidation = false;


    public function actionLogin()
    {

        // $test = Yii::$app->security->generatePasswordHash('secret');
        // var_dump($test); exit;
        $body = Yii::$app->request->post();
        $user = User::findOne(['username' => $body['username'] ?? null]);


        if ($user && Yii::$app->security->validatePassword($body['password'] ?? '', $user->password_hash)) {
            // Generate token and save
            $user->access_token = Yii::$app->security->generateRandomString();
            $user->save(false);


            return [
                'access_token' => $user->access_token,
            ];
        }


        Yii::$app->response->statusCode = 401;
        return ['error' => 'Invalid username or password'];
    }
}
