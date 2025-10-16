<?php

namespace app\services;

use Yii;
use app\models\User;
use app\models\Category;
use app\models\ExecutorCategory;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;

class UserEditService
{
  /**
   * Выполняет логику редактирования профиля пользователя.
   * Загружает текущие данные пользователя, обрабатывает POST-запрос,
   * сохраняет изменения и обновляет связанные специализации.
   *
   * @param User $user Модель текущего пользователя.
   * @return array Массив с результатом: либо данные для рендеринга формы,
   *               либо инструкция для перенаправления после успешного сохранения.
   */
  public function execute(User $user)
  {
    $user->selectedCategoryIds = $user->categories ? array_column($user->categories, 'category_id') : [];
    $allCategories = Category::find()->all();
    $user->birth_date_view = $user->formatBirthDateForView($user->birth_date);

    if (Yii::$app->request->isPost) {
      $user->scenario = 'edit';
      $postData = Yii::$app->request->post();
      unset($postData['User']['avatar']);
      $user->load($postData);
      $uploadedAvatar = UploadedFile::getInstance($user, 'avatar');

      if ($user->avatarFileEmptyOrSaved($uploadedAvatar) &&
          $user->validate() &&
          $user->save()) {
        ExecutorCategory::updateForUser($user->id, $user->selectedCategoryIds);
        return ['redirect' => ['user/view', 'id' => $user->id]];
      }
    }

    return [
      'render' => 'edit',
      'params' => [
        'user' => $user,
        'allCategories' => $allCategories,
      ],
    ];
  }
}
