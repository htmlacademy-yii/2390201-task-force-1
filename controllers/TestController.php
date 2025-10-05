<?php

namespace app\controllers;

use app\models\User;
use yii\web\Controller;
use Yii;

class TestController extends Controller
{

// Обновление паролей пользователям с id от 1 до 8 на "1234"
public function actionIndex()
  {
    $this->layout = 'landing';
    $users = [];
    for ($id=1; $id<=8; $id++) {
      $user = User::findOne($id);
      $user->password = Yii::$app->security->generatePasswordHash('1234');
      $user->save(false);
      $users[] = $user;
    }

    return $this->render('index', ['users' => $users]);
  }
}

