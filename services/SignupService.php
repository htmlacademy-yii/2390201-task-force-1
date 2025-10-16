<?php

namespace app\services;

use app\models\User;
use app\models\SignupForm;
use Yii;

class SignupService
{
  /**
   * Создаёт экземпляр модели User на основе данных из формы регистрации.
   * Хеширует пароль, устанавливает дату регистрации и аватар по умолчанию.
   *
   * @param SignupForm $signupForm Данные формы регистрации.
   * @return User Новый экземпляр модели пользователя с заполненными полями.
   */
  public function createUserFromForm(SignupForm $signupForm): User
  {
    $user = new User();

    $user->name = $signupForm->name;
    $user->email = $signupForm->email;
    $user->password = Yii::$app->security->generatePasswordHash($signupForm->password);
    $user->location_id = $signupForm->location_id;
    $user->is_executor = $signupForm->is_executor;
    $user->reg_date = date('Y-m-d H:i:s');
    $user->avatar = 'img/man-running.png';

    return $user;
  }
}
