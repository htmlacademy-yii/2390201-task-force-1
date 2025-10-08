<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "categories".
 *
 * @property int $id
 * @property string $name
 * @property string $rus_name
 *
 * @package app\models
 */
class Category extends ActiveRecord
{
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'categories';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['name', 'rus_name'], 'required'],
      [['name', 'rus_name'], 'string', 'max' => 128],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => 'ID',
      'name' => 'Специализация',
      'rus_name' => 'Название специализации',
    ];
  }
}
