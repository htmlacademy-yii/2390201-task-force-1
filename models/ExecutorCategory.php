<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "executor_category".
 *
 * @property int $id
 * @property int $user_id
 * @property int $category_id
 */
class ExecutorCategory extends ActiveRecord
{
  public static function tableName()
  {
    return 'executor_category';
  }

  public function rules()
  {
    return [
      [['user_id', 'category_id'], 'required'],
      [['user_id', 'category_id'], 'integer'],
    ];
  }

  public function attributeLabels()
  {
    return [
      'id' => 'ID',
      'user_id' => 'ID Пользователя',
      'category_id' => 'ID Специализации',
    ];
  }

  /**
   * Обновляет специализации пользователя.
   */
  public static function updateForUser(int $userId, array $categoryIds): void
  {
    static::deleteAll(['user_id' => $userId]);
    foreach ($categoryIds as $id) {
      $ec = new static();
      $ec->user_id = $userId;
      $ec->category_id = (int)$id;
      if (!$ec->save(false)){
        throw new \RuntimeException("Не удалось сохранить в БД запись специализации пользователя {$userId}");
      }
    }
  }

  public function getUser()
  {
    return $this->hasOne(User::class, ['id' => 'user_id']);
  }

  public function getCategory()
  {
    return $this->hasOne(Category::class, ['id' => 'category_id']);
  }
}
