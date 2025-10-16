<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\services\UserEditService;
use yii\web\NotFoundHttpException;

class UserController extends SecuredController
{
  /**
   * Отображает информацию о пользователе-исполнителе по указанному ID.
   *
   * @param int $id Идентификатор пользователя.
   * @return string Рендеринг представления 'view'.
   * @throws NotFoundHttpException Если пользователь не найден или не является исполнителем.
   */
  public function actionView(int $id)
  {
    $user = User::findOne($id);

    if (!$user) {
      throw new NotFoundHttpException('Пользователь с id='.$id.' не найден.');
    }

    if(!$user->is_executor) {
      throw new NotFoundHttpException('Пользователь с id='.$id.' не является исполнителем.');
    }

    return $this->render('view', [
      'user' => $user
    ]);
  }

  /**
   * Выполняет выход текущего пользователя из системы и перенаправляет на главную страницу.
   *
   * @return \yii\web\Response
   */
  public function actionLogout()
  {
    \Yii::$app->user->logout();

    return $this->goHome();
  }

  /**
   * Выполняет редактирование профиля текущего пользователя через форму редактирования.
   * Обрабатывает как отображение формы редактирования, так и сохранение изменений.
   *
   * @return string|\yii\web\Response Рендеринг формы редактирования или перенаправление после успешного сохранения.
   * @throws NotFoundHttpException Если текущий пользователь не авторизован.
   */
  public function actionEdit()
  {
    $user = Yii::$app->user->identity;
    if (!$user) {
      throw new NotFoundHttpException('Пользователь не найден.');
    }

    $service = new UserEditService();//логика редактирования вынесена в UserEditService
    $result = $service->execute($user);

    if (isset($result['redirect'])) {
      return $this->redirect($result['redirect']);
    }

    return $this->render($result['render'], $result['params']);
  }
}
