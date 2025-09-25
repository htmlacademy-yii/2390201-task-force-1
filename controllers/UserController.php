<?php

namespace app\controllers;

use app\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class UserController extends Controller
{
  // Просмотр информации о пользователе с ID=$id
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
}
