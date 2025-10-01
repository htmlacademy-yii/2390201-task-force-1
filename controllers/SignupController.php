<?php

namespace app\controllers;

use app\models\User;
use app\models\SignupForm;
use yii\web\Controller;
use Yii;

class SignupController extends Controller
{
  // Заполняем поля нового пользователя модели User из модели формы регистрации SignupForm
  // Поля, которых нет в форме, сохранятся в БД как null
  private function userFromSignupForm(SignupForm $signupForm): User
  {
    $user = new User();

    $user->name = $signupForm->name;
    $user->email = $signupForm->email;
    $user->password = Yii::$app->security->generatePasswordHash($signupForm->password);
    $user->town_id = $signupForm->town_id;
    $user->is_executor = $signupForm->is_executor;
    $user->reg_date = date('Y-m-d H:i:s');

    return $user;
  }

  // Регистрация нового пользователя
  public function actionIndex()
  {
    // Залогиненным пользователям страница регистрации недоступна
    if (!Yii::$app->user->isGuest) {
      return $this->redirect(['tasks/index']);
    }

    $signupForm = new SignupForm();
    if (Yii::$app->request->isPost) {
      $signupForm->load(Yii::$app->request->post());
      if ($signupForm->validate()) {
        $user = $this->userFromSignupForm($signupForm);
        $user->save(false);
        return $this->goHome();
      }
    }

    return $this->render('index', ['signupForm' => $signupForm]);
  }
}
