<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class User extends ActiveRecord implements \yii\web\IdentityInterface
{
    public static function tableName()
    {
        return '{{%user}}';
    }

    public function rules()
    {
        return [
            [['username', 'password_hash'], 'required'],
            [['username'], 'unique'],
            [['access_token'], 'string', 'max' => 255],
        ];
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }


    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }


    public function getId()
    {
        return $this->id;
    }

    public function removeAccessToken()
    {
        $this->access_token = null;
        $this->save(false);
    }
    
    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString();
        $this->save(false);
        return $this->access_token;
    }

    public function getAuthKey() {}
    public function validateAuthKey($authKey) {}
}
