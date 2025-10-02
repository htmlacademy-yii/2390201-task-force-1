<?php
namespace app\controllers;

use Yii;
use app\models\LoginForm;
use yii\web\Controller;

class LandingController extends Controller
{
  // Со страницы лендинга осуществляется вход на сайт через модальное окно с формой входа.
  // Это окно реализовано в представлении через html-css-js
  public function actionIndex()
  {
    // Залогиненным пользователям вместо лендинга (и входа на сайт) предлагается страница просмотра списка задач
    if (!Yii::$app->user->isGuest) {
      return $this->redirect(['tasks/index']);
    }

    $this->layout = 'landing';

    $loginForm = new LoginForm();

    // Обрабатываем модальное окно входа на сайт, если оно вызвано
    if (Yii::$app->request->getIsPost()) {
      $loginForm->load(Yii::$app->request->post());
      if ($loginForm->validate()) {
        $user = $loginForm->getUser();
        Yii::$app->user->login($user);
        return $this->redirect(['tasks/index']);
      }
    }

    return $this->render('index', ['loginForm' => $loginForm]);
  }
}
