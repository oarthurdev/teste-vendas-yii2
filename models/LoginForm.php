<?php

namespace app\models;

use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;

    public function rules() {
        return [
            [['username', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    public function validatePassword($attribute, $params) {
        Yii::info("Validando senha para usuário: " . $this->username, __METHOD__);
    
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user) {
                $this->addError($attribute, 'No user found with this username.');
                return;
            }
    
            if (!$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect password.');
            }
        }
    }    

    public function login() {
        if ($this->validate()) {
            $user = User::findOne(['username' => $this->username]);
            if ($user && Yii::$app->security->validatePassword($this->password, $user->password_hash)) {
                $user->auth_key = Yii::$app->security->generateRandomString();
                $user->save(false);
    
                return Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0);
            } else {
                return false;
            }
        }
    
        return false;
    }

    protected function getUser() {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }
        return $this->_user;
    }
}
