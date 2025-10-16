<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\SignupForm;
use app\services\SignupService;

class SignupController extends Controller
{
  /**
   * Отображает форму регистрации и обрабатывает отправку данных.
   * Если пользователь уже авторизован, перенаправляет на главную страницу задач.
   * При успешной валидации формы создаёт нового пользователя и выполняет вход.
   *
   * @return \yii\web\Response|\yii\web\View
   */
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
        $signupService = new SignupService();
        $user = $signupService->createUserFromForm($signupForm);
        $user->save(false);
        return $this->goHome();
      }
    }

    return $this->render('index', ['signupForm' => $signupForm]);
  }
}
