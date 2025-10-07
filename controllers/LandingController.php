<?php
namespace app\controllers;

use Yii;
use app\models\LoginForm;
use app\models\User;
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

  /**
  * Объявляет действия контроллера, предоставляемые расширением yii2-authclient.
  * Действие 'auth' обрабатывает OAuth-авторизацию через сторонние сервисы (например, ВКонтакте).
  * После успешной авторизации вызывается метод onAuthSuccess для обработки данных пользователя.
  */
  public function actions()
  {
    return [
      'auth' => [
        'class' => 'yii\authclient\AuthAction',
        'successCallback' => [$this, 'onAuthSuccess'],
      ],
    ];
  }

  /**
   * Обработка данных пользователя, полученных после успешной авторизации ВКонтакте
   */
  public function onAuthSuccess($client)
  {
    $attributes = $client->getUserAttributes();

    $vkId = $attributes['id'] ?? null;
    if (!$vkId) {
      Yii::$app->session->setFlash('error', 'Не удалось получить данные из ВКонтакте');
      return $this->goHome();
    }

    $user = User::findOrCreateFromVk($attributes);

    if (!$user) {
      Yii::$app->session->setFlash('error', 'Ошибка при создании или обновлении пользователя');
      return $this->goHome();
    }

    Yii::$app->user->login($user);
    return $this->redirect(['tasks/index']);
  }
}
