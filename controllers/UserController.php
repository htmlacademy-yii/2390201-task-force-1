<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\Category;
use app\models\ExecutorCategory;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\web\ForbiddenHttpException;

class UserController extends SecuredController
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

  // Выход пользователя с сайта, перенаправление на главную страницу
  public function actionLogout()
  {
    \Yii::$app->user->logout();

    return $this->goHome();
  }

  /**
   * Редактирование текущим пользователем информации о себе
   */
  public function actionEdit()
  {
    $user = Yii::$app->user->identity;
    if (!$user) {
      throw new NotFoundHttpException('Пользователь не найден.');
    }
    // Инициализируем вирт. поле специализаций, все существующие специализации, вирт.поле даты рождения из БД
    $user->selectedCategoryIds = $user->categories ? array_column($user->categories, 'category_id') : [];
    $allCategories = Category::find()->all();
    $user->birth_date_view = $user->formatBirthDateForView($user->birth_date);

    if (Yii::$app->request->isPost) {
      $user->scenario = 'edit';
      $postData = Yii::$app->request->post();
      unset($postData['User']['avatar']); // иначе $user->load() обнулит avatar, загруженный из БД
      $user->load($postData);
      $uploadedAvatar = UploadedFile::getInstance($user, 'avatar');

      if ($user->avatarFileEmptyOrSaved($uploadedAvatar) &&
          $user->validate() &&
          $user->save()) {
        ExecutorCategory::updateForUser($user->id, $user->selectedCategoryIds);
        return $this->redirect(['view', 'id' => $user->id]);
      }
    }

    return $this->render('edit', [
      'user' => $user,
      'allCategories' => $allCategories,
    ]);
  }
}

