<?php

namespace app\models;

use Yii;
use yii\base\Model;

// Отдельная модель для заполнения формы регистрации нового пользователя
class SignupForm extends Model
{
  public $name;
  public $email;
  public $password;
  public $password_repeat;
  public $location_id;
  public $is_executor = false;

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['name', 'email', 'password', 'password_repeat', 'location_id'], 'required', 'message' => 'Поле не может быть пустым'],
      ['email', 'email'],
      ['email', 'unique', 'targetClass' => User::class, 'targetAttribute' => 'email', 'message' => 'Этот email уже зарегистрирован.'],
      ['password_repeat', 'compare', 'compareAttribute' => 'password', 'message' => 'Пароли не совпадают.'],
      ['location_id', 'exist', 'targetClass' => Location::class, 'targetAttribute' => 'id'],
      ['is_executor', 'boolean'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'name' => 'Ваше имя',
      'email' => 'Email',
      'password' => 'Пароль',
      'password_repeat' => 'Повтор пароля',
      'location_id' => 'Город',
      'is_executor' => '', //делаем в представлении кастомную метку
    ];
  }
}
