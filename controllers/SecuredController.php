<?php
namespace app\controllers;

use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Базовый контроллер для страниц, доступных только авторизованным пользователям.
 *
 * Все контроллеры, унаследованные от этого класса, автоматически защищаются
 * с помощью фильтра AccessControl: доступ разрешён только аутентифицированным
 * пользователям (роль '@'). 
 *
 * Контроллеры публичных страниц: LandingController, и регистрация - SignupController
 * НЕ должны наследоваться от этого класса.
 *
 * @see yii\filters\AccessControl
 */
abstract class SecuredController extends Controller
{
  public function behaviors()
  {
    return [
      'access' => [
        'class' => AccessControl::class,
        'rules' => [
          [
            'allow' => true,
            'roles' => ['@'],
          ],
        ],
      ],
    ];
  }
}
